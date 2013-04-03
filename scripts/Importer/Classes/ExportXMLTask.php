<?php

namespace Classes;

use Zend\Di\Di;

use Zend\Db\ResultSet\ResultSet;

use Classes\Entities\MetaData as MetaDataEntity;

use Classes\Db\MetaData as MetaDataDb;
use Classes\Db\Item     as ItemDb;

use Classes\Helpers\MwXml;

class ExportXMLTask extends TaskAbstract{

	/* @var $oMetaDataItemEntity MetaDataItemEntity */
	private $oMetaDataItemEntity;

	private $sXMLExportPath;

	private $sImageExportPath;

	private $sArchivePath;

	private $sPagePrefix;

	private $iJoBQueueId;

	/* @var $oDomDocument DOMDocument */
	private $oDomDocument;

	/* @var $oMwXml MwXml */
	private $oMwXml;


	public function __construct( Di $oDi
								,   $aSectionConfig
								,   $iJobQueueId ){

		parent::__construct( $oDi );

		$this->sXMLExportPath   = $aSectionConfig[ 'path.xml.export' ];

		$this->sImageExportPath = $aSectionConfig[ 'path.image.export' ];
		$this->sArchivePath     = $aSectionConfig[ 'path.archives' ];

		$this->iJobQueueId      = $iJobQueueId;

		/* @var MwXml */
		$this->oMwXml           = $oDi->get( 'Classes\Helpers\MwXml' );

	}



	/*
	 *
	 */
	public function Execute(){

		$this->oDomDocument         = $this->oMwXml->InitialiseDocument();

		$aMetaDataCollection        = $this->GetMetaDataItems();

		$aMetaDataCollectionMarkers = $this->GetMetaDataCollectionMarkers( $aMetaDataCollection );

		$this->ProcessMetaDataItems( $aMetaDataCollection, $aMetaDataCollectionMarkers );

	}


	/*
	 *
	 */
	private function ProcessMetaDataItems( $aMetaDataCollection
										 , $aMetaDataCollectionMarkers ){

		$counter = 0;

		$sCurrentBoxNumber = '';

		/* @var $oMetaDataEntity MetaDataEntity */
		foreach ( $aMetaDataCollection as $key => $oMetaDataEntity ){

			$sBoxNumber = $oMetaDataEntity->getBoxNumber();

			if( $sBoxNumber != $sCurrentBoxNumber ){
				$iInitialKey       = $key;
				$sCurrentBoxNumber = $sBoxNumber;
			}


			$iNextEntityIndex = $key + 1;

			// If the next item does not exist then point to the start

			if( array_key_exists( $iNextEntityIndex, $aMetaDataCollection ) === false ){
				$iNextEntityIndex = $iInitialKey;
			}


			$iPrevEntityIndex = $key - 1;

			// If the previous item does not exist then point to the end

			if( array_key_exists( $iPrevEntityIndex, $aMetaDataCollection ) === false ){
				$iPrevEntityIndex = $aMetaDataCollectionMarkers[ $sBoxNumber ][ 'end'];
			}

			$oNextEntity = $aMetaDataCollection[ $iNextEntityIndex ];
			$oPrevEntity = $aMetaDataCollection[ $iPrevEntityIndex ];

			$sMetaDataText = $this->oMwXml->CreateMetaDataText(
																$oNextEntity
															  , $oMetaDataEntity
															  , $oPrevEntity );

			$oMetaDataTextNode = $this->oMwXml->CreatePageElement( $oMetaDataEntity
																 , $this->oDomDocument
																 , $sMetaDataText );

			$oPageText     = $this->oMwXml->CreatePageText( $oMetaDataEntity );

			$oPageTextNode = $this->oMwXml->CreatePageElement(
																$oMetaDataEntity
															  , $this->oDomDocument
															  , $oPageText
																);

		}

		if( file_exists( $this->sXMLExportPath )){
			unlink( $this->sXMLExportPath );
			touch( $this->sXMLExportPath );
		}

		$this->oDomDocument->formatOutput = true;

		$this->oDomDocument->save( $this->sXMLExportPath );

		$this->UpdateJobProcessStatus( $aMetaDataCollection );


	}


	private function UpdateJobProcessStatus( $aMetaDataCollection ){


		/* @var $oMetaDataEntity MetaDataEntity */
		foreach ( $aMetaDataCollection as $key => $oMetaDataEntity ){


			$iItemId = $oMetaDataEntity->getItemId();


			$this->oItemDb->UpdateJobProcessStatusByItemId(
														  $iItemId
														, 'import_mw'
														, 'queued'
														, 'export'
														, 'queued' );

		}

		$iJobQueueId = $this->iJoBQueueId;

		$this->oMetaDataDb->UpdateProcessStatusByJobQueueId(
															$iJobQueueId
														  , 'import_mw'
														  , 'queued'
														  , 'export'
														  , 'queued' );

	}


	/*
	 *@return array
	 */
	private function GetMetaDataItems( ){

		$rMetaData = $this->GetMetaDataItemsResultSet();

		$aMetaDataCollection = array();

		$aMetaDataCollectionMarkers = array();


		/* @var $oMetaDataItemEntity MetaDataEntity */
		while ( $oMetaDataItemEntity = $rMetaData->getResource()->fetchObject( 'Classes\Entities\MetaData' ) ){
			$aMetaDataCollection[] = $oMetaDataItemEntity;
		}


		return $aMetaDataCollection;

	}

	/*
	 * @return array
	 */
	private function GetMetaDataCollectionMarkers( array $aMetaDataCollection ){

		if( count ( $aMetaDataCollection)  === 0 ){
			return array();
		}

		$sCurrentBox   = NULL;
		$sCurrentIndex = NULL;

		/* @var $oMetaDataItemEntity MetaDataEntity */
		foreach ( $aMetaDataCollection as $oMetaDataItemEntity ){

			$sBox = $oMetaDataItemEntity->getBoxNumber();

			if( $sBox != $sCurrentBox ){
				$i = 0;

				$aMetaDataCollectionMarkers[ $sBox ][ 'start' ] = $i;

				if( $sCurrentBox !== NULL and $sCurrentIndex !== NULL ){

					$aMetaDataCollectionMarkers[ $sCurrentBox ][ 'end' ] =  $sCurrentIndex;

				}

			}

			$sCurrentBox   = $sBox;

			$sCurrentIndex = $i;

			$i++;

		}

		if( $sCurrentBox !== NULL and $sCurrentIndex !== NULL ){

			$aMetaDataCollectionMarkers[ $sCurrentBox ][ 'end' ] =  $sCurrentIndex;
		}

		return $aMetaDataCollectionMarkers;
	}


	/*
	 *
	 */
	private function GetMetaDataItemsResultSet(){

		return $this->oMetaDataDb->GetJobMetaDataItems(
														  $this->iJobQueueId
														, 'export'
														, 'queued'
													  );

	}

	/*
	 *
	*/
	protected function ExportXMLPseudoSetterForAutoComplete( MwXml $oMwXml ){

		$this->oMwXml = $oMwXml;

	}



}
































