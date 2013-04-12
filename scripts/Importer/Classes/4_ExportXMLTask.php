<?php

namespace Classes;

use Zend\Di\Di;

use Zend\Db\ResultSet\ResultSet;

use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Folio    as FolioItemEntity;

use Classes\Db\Box   as BoxDb;
use Classes\Db\Folio as FolioDb;
use Classes\Db\Item  as ItemDb;

use Classes\Mappers\MwXml;

use Classes\Exceptions\Importer as ImporterException;

class ExportXMLTask extends TaskAbstract{

	/* @var $oFolioItemEntity FolioItemEntity */
	private $oFolioItemEntity;

	private $sXMLExportPath;

	private $sImageExportPath;

	private $sArchivePath;

	/* @var JoBItemsToMwXml */
	private $oMapper;


	public function __construct(  Di             $oDi
								,                $aConfig
								, JobQueueEntity $oJobQueueEntity ){

		parent::__construct( $oDi );

		$this->sXMLExportPath   = $aConfig[ 'path.xml.export' ];
		$this->sArchivePath     = $aConfig[ 'path.archive' ];

		$this->iJobQueueId      = $oJobQueueEntity->getId();

		/* @var MwXml */
		$this->oMapper          = $oDi->get( 'Classes\Mappers\JobItemsToMwXml' );

		$this->sProcess         = 'export';

		$this->sPreviousProcess = 'slice';

	}



	/*
	 *
	 */
	public function Execute(){

		try {

			$sProcess    = $this->sProcess;
			$iJobQueueId = $this->iJobQueueId;

			// Pre-checks
			$this->CheckPaths();

			$aFolioCollection        = $this->GetFolioItems();

			$aFolioCollectionMarkers = $this->GetFolioCollectionRanges( $aFolioCollection );

			// Only do this after the resultset has been created
			$this->oBoxDb->FlagJobProcessAsStarted( $iJobQueueId, $sProcess );

			$this->ProcessFolioItems( $aFolioCollection, $aFolioCollectionMarkers );

			$this->ExportXml();

			$this->oBoxDb->FlagJobProcessAsCompleted( $iJobQueueId, $sProcess );

		} catch ( Exception $oException ) {
			$this->HandleError( $oException, $oJobQueueEntity );
		}


	}


	/*
	 *
	*/
	private function CheckPaths(){

		$this->oFile->CheckExists( 'MLExportPath', $this->sXMLExportPath );
		$this->oFile->CheckExists( 'ArchivePath', $this->sArchivePath );

	}


	/*
	 *
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
	 *@return array
	 */
	private function GetFolioItems( ){

		$rFolios = $this->GetFolioItemsResultSet();

		$aFolioCollection = array();

		$aFolioCollectionMarkers = array();


		/* @var $oFolioItemEntity FolioEntity */
		while ( $oFolioItemEntity = $rFolios->getResource()->fetchObject( 'Classes\Entities\Folio' ) ){
			$aFolioCollection[] = $oFolioItemEntity;
		}


		return $aFolioCollection;

	}

	/*
	 * @return array
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
	 *
	 */
	private function GetFolioItemsResultSet(){

		return $this->oBoxDb->GetJobItems(
										  $this->iJobQueueId
										, 'slice'
										, 'completed'
									  );

	}


	private function ExportXml(){

		$oDomDocument = $this->oMapper->GetDocument();

		$oDomDocument->formatOutput = true;

		$sXmlFileName = $this->sXMLExportPath . DIRECTORY_SEPARATOR . $this->iJobQueueId . '.xml';

		// Remove any existing file

		if( file_exists( $sXmlFileName )){
			unlink( $sXmlFileName );
		}

		$oDomDocument->save( $sXmlFileName );


	}

	/*
	 *
	*/
	protected function ExportXMLPseudoSetterForAutoComplete( MwXml $oMwXml ){

		$this->oMapper = $oMwXml;

	}



}
































