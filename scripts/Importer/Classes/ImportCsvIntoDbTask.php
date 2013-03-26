<?php

namespace Classes;

use Classes\Mappers\CsvRowToMetaDataEntity as CsvRowToMetaDataEntityMapper;
use Classes\Db\Item as ItemDb;
use Classes\Db\MetaData as MetaDataDb;

use \Classes\Entities\MetaData as MetaDataEntity;
use \Classes\Entities\Item as ItemEntity;

require_once 'Helpers\Encoding.php';

class ImportCsvIntoDbTask{


	/* @var ItemDb */
	private $oItemDb;

	/* @var MetaDataDb */
	private $oMetaDataDb;

	/* @var Helpers\File */
	private $oFileHelper;


	/* @var $oCsvRowToMetatDataEntityMapper CsvRowToMetaDataEntityMapper */
	private $oCsvRowToMetatDataEntityMapper;


	/* @var Helpers\Logger  */
	private $oLogger;

	private $sCsvFilePath;

	private $iJobQueueId;

	/* @var ItemEntity */
	private $oItemEntity;

	public function __construct(
								  MetaDataDb                   $oMetaDataDb
			                    , ItemDb                       $oItemDb
								, Helpers\File                 $oFileHelper
								, CsvRowToMetaDataEntityMapper $oCsvRowToMetatDataEntityMapper
								, Helpers\Logger               $oLogger
								,                              $sCsvFilePath
								,                              $iJobQueueId ){

		$this->oMetaDataDb                    = $oMetaDataDb;
		$this->oItemDb						  = $oItemDb;
		$this->oFileHelper                    = $oFileHelper;
		$this->oCsvRowToMetatDataEntityMapper = $oCsvRowToMetatDataEntityMapper;

		$this->sCsvFilePath                   = $sCsvFilePath;

		$this->oLogger                        = $oLogger;

		$this->iJobQueueId                    = $iJobQueueId;

		$this->oItemEntity                    = new ItemEntity();

	}

	/*
	 *
	 */
	public function Execute(){

		$sFilePath = $this->sCsvFilePath;

		$hHandle   = $this->oFileHelper->GetFileHandle( $sFilePath );

		$this->IterateCsvFileAndInsertRowsIntoDb( $hHandle );

	}


	/*
	 *
	 */
	private function IterateCsvFileAndInsertRowsIntoDb( $hHandle ){

		$oMetadata   = new Entities\MetaData();

		$iCurrentRow = 1;

		$aAssocRow   = array();

		while ( $aRow = fgetcsv ( $hHandle, 4000, '|' ) ){

			$iNumberOfFields = count( $aRow );

			// Create an associative array

			if( $iCurrentRow == 1 ){
				for ( $i = 0; $i < $iNumberOfFields; $i++ ){
					$aHeader[ $i ] = $aRow[ $i ];
				}
				$iCurrentRow = 2;
				continue;
			}

			for ( $i = 0; $i < $iNumberOfFields; $i++ ){
				$aAssocRow[ $aHeader[ $i ] ] = $aRow[ $i ];
			}

			$oCsvRowToMetaDataMapper = $this->oCsvRowToMetatDataEntityMapper;

			$oMappedMetaDataEntity = $oCsvRowToMetaDataMapper->Map( $aAssocRow );

			$oMappedMetaDataEntity->setJobQueueId( $this->iJobQueueId );

			if( $oMappedMetaDataEntity instanceof Entities\MetaData ){
				$oMappedMetaDataEntity = $this->oMetaDataDb->Insert( $oMappedMetaDataEntity );
			}


			$this->InsertFolioItems( $oMappedMetaDataEntity );

			$iCurrentRow++;


			// REMOVE !!!!
			if( $iCurrentRow > 3){
				exit;
			}

		}
	}


	/*
	 *
	 */
	private function InsertFolioItems( MetaDataEntity $oMappedMetaDataEntity ){

		$oItemEntity    = $this->oItemEntity;

		$iMetaDataId    = $oMappedMetaDataEntity->getId();

		$oItemEntity->setMetaDataId( $iMetaDataId );

		$iNumberOfPages = $oMappedMetaDataEntity->getNumberOfPages();

		for ( $i = 1; $i < $iNumberOfPages + 1; $i++ ){

			$sItemNumber = (string) $i;

			$sItemNumber = str_pad( $sItemNumber, 3, '0', STR_PAD_LEFT );

			$oItemEntity->setItemNumber( $sItemNumber );

			$this->oItemDb->Insert( $oItemEntity );

		}

		$this->oMetaDataDb->UpdateProcessStatus( $iMetaDataId, 'insert', 'completed' );

	}















































}