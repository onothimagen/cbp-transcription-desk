<?php

/**
 * Copyright (C) University College London
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License Version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 * @package CBP Transcription
 * @subpackage Importer
 * @version 1.0
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  University College London
 */

namespace Classes;

use Zend\Di\Di;

use Zend\Db\ResultSet\ResultSet;

use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Folio    as FolioItemEntity;

use Classes\Db\Box   as BoxDb;
use Classes\Db\Folio as FolioDb;
use Classes\Db\Item  as ItemDb;

use Classes\Mappers\JoBItemsToMwXml;

use Classes\Exceptions\Importer as ImporterException;

/*
 * This drills down from the job, box, folio and item using methods in the TaskAbstract
 * It constructs a DOM element from each item and adds it to a DOM document
 * Writes the DOM document to a file ready for import into media wiki
 * It will not add items that already exist in MediaWiki
 */
class ExportXMLTask extends TaskAbstract{

	/* @var $oFolioItemEntity FolioItemEntity */
	private $oFolioItemEntity;

	private $sXMLExportPath;

	private $sImageExportPath;

	private $sArchivePath;

	/* @var $oMapper JoBItemsToMwXml  */
	private $oMapper;


	/*
	* @param Di				$oDi
	* @param string[] 		$aConfig
	* @param JobQueueEntity	$oJobQueueEntity
	* @return void
	*/
	public function __construct(  Di             $oDi
								,                $aConfig
								, JobQueueEntity $oJobQueueEntity ){

		parent::__construct( $oDi );

		$this->sXMLExportPath   = $aConfig[ 'path.xml.export' ];
		$this->sArchivePath     = $aConfig[ 'path.archive' ];

		$this->oMapper          = $oDi->get( 'Classes\Mappers\JobItemsToMwXml' );

		$this->sProcess         = 'export';

		$this->sPreviousProcess = 'slice';

		$this->iJobQueueId      = $oJobQueueEntity->getId();

	}



	/*
	 * Entry point to start task
	 *
	 * @return void
	 */
	public function Execute(){

		try {

	        $this->oLogger->ConfigureLogger( 'jobs', $this->iJobQueueId );

			$sProcess    = $this->sProcess;
			$iJobQueueId = $this->iJobQueueId;

			// Pre-checks
			$this->CheckPaths();

			/* An array of Folio item entities */
			$aFolioCollection        = $this->GetFolioItems();


			$aFolioCollectionMarkers = $this->GetFolioCollectionRanges( $aFolioCollection );

			// Only do this after the resultset has been created
			$this->oBoxDb->FlagJobProcessAsStarted( $iJobQueueId, $sProcess );

			$this->ProcessFolioItems( $aFolioCollection, $aFolioCollectionMarkers );

			$this->WriteXml();

			$this->oBoxDb->FlagJobProcessAsCompleted( $iJobQueueId, $sProcess );

		} catch ( ImporterException $oException ) {
			$this->HandleError( $oException, $oJobQueueEntity );
		}
	}


	/*
	 * @return void
	*/
	private function CheckPaths(){

		$this->oFile->CheckDirExists( $this->sXMLExportPath );
		$this->oFile->CheckDirExists( $this->sArchivePath );

	}


	/*
	 * This loops through the array of folio item entities
	 * It identified its neighbouring entities i.e. 'previous' and 'next' by index
	 * It resolves 'previous' and 'next' for those items at the beginning or end
	 * by the 'first' item referencing the 'last' as its 'previous' neighbour
	 * by the 'last' item referencing the 'first' as its 'next' neighbour
	 * Once the previous and last neighbour entities have been identified
	 * then then these are passed to the XML mapper to be added to the XML DOM
	 *
	 * @param FolioEntity[] $aFolioCollection All the folio item entities in array
	 * @param string[] $aFolioCollectionMarkers A metadata array that stores box 'start' and 'end' items
	 */
	private function ProcessFolioItems( $aFolioCollection
									  , $aFolioCollectionMarkers ){

		$counter = 0;

		$sCurrentBoxNumber = '';

		/* @var $oEntity FolioItemEntity */
		foreach ( $aFolioCollection as $key => $oEntity ){

			$sBoxNumber = $oEntity->getBoxNumber();

			// Start of a new box

			if( $sBoxNumber != $sCurrentBoxNumber ){
				$iInitialKey       = $key;
				$sCurrentBoxNumber = $sBoxNumber;
			}

			$iNextEntityIndex = $key + 1;

			// If the next item does not exist then point to the start

			if( array_key_exists( $iNextEntityIndex, $aFolioCollection ) === false ){
				$iNextEntityIndex = $iInitialKey;
			}


			$iPrevEntityIndex = $key - 1;

			// If the previous item does not exist then point to the end

			if( array_key_exists( $iPrevEntityIndex, $aFolioCollection ) === false ){
				$iPrevEntityIndex = $aFolioCollectionMarkers[ $sBoxNumber ][ 'end'];
			}

			$oNextEntity = $aFolioCollection[ $iNextEntityIndex ];
			$oPrevEntity = $aFolioCollection[ $iPrevEntityIndex ];

			// Generate XML
			$this->oMapper->CreateItemPages( $oNextEntity, $oEntity, $oPrevEntity );

		}

	}


	/*
	 * Gets a result set of folio items and converts them into an array of Folio Item Entities
	 *
	 * @return FolioItemEntity[]
	 */
	private function GetFolioItems( ){

		$rFolios = $this->GetFolioItemsResultSet();

		$aFolioCollection        = array();

		$aFolioCollectionMarkers = array();


		/* @var $oFolioItemEntity FolioEntity */
		while ( $oFolioItemEntity = $rFolios->getResource()->fetchObject( 'Classes\Entities\Folio' ) ){
			$aFolioCollection[] = $oFolioItemEntity;
		}

		return $aFolioCollection;

	}

	/*
	 * This loops through the all the folio items entities and
	 * creates a new array that identified those that are at the
	 * start and end of the box
	 *
	 * @return FolioItemEntity[]
	 * @return string[]
	 */
	private function GetFolioCollectionRanges( array $aFolioCollection ){

		if( count ( $aFolioCollection)  === 0 ){
			return array();
		}

		$sCurrentBox   = NULL;
		$sCurrentIndex = NULL;

		/* @var $oEntity FolioItemEntity */
		foreach ( $aFolioCollection as $oEntity ){

			$sBox = $oEntity->getBoxNumber();

			// Start of a new box

			if( $sBox != $sCurrentBox ){
				$i = 0;

				$aFolioCollectionMarkers[ $sBox ][ 'start' ] = $i;

				if( $sCurrentBox !== NULL and $sCurrentIndex !== NULL ){

					$aFolioCollectionMarkers[ $sCurrentBox ][ 'end' ] =  $sCurrentIndex;

				}

			}

			$sCurrentBox   = $sBox;

			$sCurrentIndex = $i;

			$i++;

		}

		if( $sCurrentBox !== NULL and $sCurrentIndex !== NULL ){

			$aFolioCollectionMarkers[ $sCurrentBox ][ 'end' ] =  $sCurrentIndex;
		}

		return $aFolioCollectionMarkers;
	}


	/*
	 * Get each items with its meta data in numerical order
	 *
	 * @return Result
	 */
	private function GetFolioItemsResultSet(){

		return $this->oBoxDb->GetJobItems( $this->iJobQueueId
									     , $this->sPreviousProcess
									     , $this->sProcess );
	}



	/*
	 * @return void
	 */
	private function WriteXml(){

		$oDomDocument = $this->oMapper->GetDocument();

		$oDomDocument->formatOutput = true;

		$sXmlFileName = $this->sXMLExportPath . DIRECTORY_SEPARATOR . $this->iJobQueueId . '.xml';

		// Remove any existing file

		if( file_exists( $sXmlFileName )){
			if( !unlink( $sXmlFileName ) ) {
				throw new ImporterException( 'ExportXml() unable to unlink ' . $sXmlFileName );
			}
		}

		$oDomDocument->save( $sXmlFileName );


	}


	/*
	 * Stub method for problems with IDE autocomplete. Can be deleted.
	 *
	 * @return void
	*/
	protected function ExportXMLPseudoSetterForAutoComplete( JoBItemsToMwXm $oMapper  ){

		$this->oMapper = $oMapper;

	}



}
































