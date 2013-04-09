<?php

namespace Classes;

use Zend\Di\Di;

use Zend\Db\ResultSet\ResultSet;

use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Folio    as FolioEntity;

use Classes\Db\Box   as BoxDb;
use Classes\Db\Folio as FolioDb;
use Classes\Db\Item  as ItemDb;

use Classes\Helpers\MwXml;

class ExportXMLTask extends TaskAbstract{

	/* @var $oFolioItemEntity FolioItemEntity */
	private $oFolioItemEntity;

	private $sXMLExportPath;

	private $sImageExportPath;

	private $sArchivePath;

	private $sPagePrefix;

	private $iJobQueueId;

	/* @var $oDomDocument DOMDocument */
	private $oDomDocument;

	/* @var $oMwXml MwXml */
	private $oMwXml;


	public function __construct(  Di             $oDi
								,                $aSectionConfig
								, JobQueueEntity $oJobQueueEntity ){

		parent::__construct( $oDi );

		$this->sXMLExportPath   = $aSectionConfig[ 'path.xml.export' ];

		$this->sImageExportPath = $aSectionConfig[ 'path.image.export' ];
		$this->sArchivePath     = $aSectionConfig[ 'path.archive' ];

		$this->iJobQueueId      = $oJobQueueEntity->getId();

		/* @var MwXml */
		$this->oMwXml           = $oDi->get( 'Classes\Helpers\MwXml' );

		$this->sProcess        = 'export';

	}



	/*
	 *
	 */
	public function Execute(){


		$this->oDomDocument      = $this->oMwXml->InitialiseDocument();

		$aFolioCollection        = $this->GetFolioItems();

		$aFolioCollectionMarkers = $this->GetFolioCollectionMarkers( $aFolioCollection );

		$this->ProcessFolioItems( $aFolioCollection, $aFolioCollectionMarkers );

		$this->ExportXml();


	}


	/*
	 *
	 */
	private function ProcessFolioItems( $aFolioCollection
										 , $aFolioCollectionMarkers ){

		$counter = 0;

		$sCurrentBoxNumber = '';

		/* @var $oFolioEntity FolioEntity */
		foreach ( $aFolioCollection as $key => $oFolioEntity ){

			$sBoxNumber = $oFolioEntity->getBoxNumber();

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

			$sFolioText = $this->oMwXml->CreateFolioText(
																$oNextEntity
															  , $oFolioEntity
															  , $oPrevEntity );

			$oFolioTextNode = $this->oMwXml->CreatePageElement( $oFolioEntity
																 , $this->oDomDocument
																 , $sFolioText );

			$oPageText     = $this->oMwXml->CreatePageText( $oFolioEntity );

			$oPageTextNode = $this->oMwXml->CreatePageElement(
																$oFolioEntity
															  , $this->oDomDocument
															  , $oPageText
																);

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
	private function GetFolioCollectionMarkers( array $aFolioCollection ){

		if( count ( $aFolioCollection)  === 0 ){
			return array();
		}

		$sCurrentBox   = NULL;
		$sCurrentIndex = NULL;

		/* @var $oFolioItemEntity FolioEntity */
		foreach ( $aFolioCollection as $oFolioItemEntity ){

			$sBox = $oFolioItemEntity->getBoxNumber();

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

		$this->oDomDocument->formatOutput = true;

		$sXmlFileName = $this->sXMLExportPath . DIRECTORY_SEPARATOR . $this->iJobQueueId . '.xml';

		// Remove any existing file

		if( file_exists( $sXmlFileName )){
			unlink( $sXmlFileName );
		}

		$this->oDomDocument->save( $sXmlFileName );


	}

	/*
	 *
	*/
	protected function ExportXMLPseudoSetterForAutoComplete( MwXml $oMwXml ){

		$this->oMwXml = $oMwXml;

	}



}
































