<?php

namespace Classes;

use Zend\Di\Di;

use Classes\Mappers\CsvRowToMetaDataEntity as CsvRowToMetaDataEntityMapper;

use Classes\Db\JobQueue as JobQueueDb;
use Classes\Db\MetaData as MetaDataDb;
use Classes\Db\Item     as ItemDb;

use Classes\Entities\MetaData as MetaDataEntity;
use Classes\Entities\Item     as ItemEntity;

use Classes\Helpers\File as File;
use Classes\Helpers\File as Logger;

use Classes\Exceptions\Importer as ImporterException;

class ImportCsvIntoDbTask extends TaskAbstract{


	/* @var $oCsvRowToMetatDataEntityMapper CsvRowToMetaDataEntityMapper */
	private $oCsvRowToMetatDataEntityMapper;

	private $sCsvFilePath;

	private $iJobQueueId;

	/* @var ItemEntity */
	private $oMetaDataEntity;

	/* @var ItemEntity */
	private $oItemEntity;

	public function __construct(  Di						   $oDi
								, CsvRowToMetaDataEntityMapper $oCsvRowToMetatDataEntityMapper
								,                              $aSectionConfig
								,                              $iJobQueueId ){

		parent::__construct( $oDi );

		$this->oCsvRowToMetatDataEntityMapper = $oCsvRowToMetatDataEntityMapper;

		$this->sCsvFilePath    = $aSectionConfig[ 'path.csv.import' ];

		$this->iJobQueueId     = $iJobQueueId;

		$this->oMetaDataEntity = new MetaDataEntity();

		$this->oItemEntity     = new ItemEntity();




	}

	/*
	 *
	 */
	public function Execute(){

		$sFilePath = $this->sCsvFilePath;

		$hHandle   = $this->oFile->GetFileHandle( $sFilePath );

		$this->IterateCsvFileAndInsertRowsIntoDb( $hHandle );
	}


	/*
	 *
	 */
	private function IterateCsvFileAndInsertRowsIntoDb( $hHandle ){

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

			if( $oMappedMetaDataEntity instanceof MetaDataEntity === false ){
				throw new ImporterException( '$oMappedMetaDataEntity returned from mapper is not an instance of CsvRowToMetaDataEntityMapper' );
			}

			$oMappedMetaDataEntity->setJobQueueId( $this->iJobQueueId );

			$oMappedMetaDataEntity = $this->oMetaDataDb->Insert( $oMappedMetaDataEntity );

			$this->InsertFolioItems( $oMappedMetaDataEntity );

			$iCurrentRow++;


			// REMOVE !!!!
			if( $iCurrentRow > 3){
				return;
			}

		}

		echo 'IterateCsvFileAndInsertRowsIntoDb completed' . "\n";
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

		$this->oMetaDataDb->UpdateProcessStatus( $iMetaDataId, 'slice', 'queued' );

		echo 'InsertFolioItems completed' . "<br />";

	}


}

