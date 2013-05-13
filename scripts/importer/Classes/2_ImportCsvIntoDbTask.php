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
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  University College London
 */

namespace Classes;

use Zend\Di\Di;

use Classes\Mappers\CsvRowToFolioEntity;

use Classes\Db\JobQueue as JobQueueDb;
use Classes\Db\Box      as BoxDb;
use Classes\Db\Folio    as FolioDb;
use Classes\Db\Item     as ItemDb;
use Classes\Db\ErrorLog as ErrorLogDb;

use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Box      as BoxEntity;
use Classes\Entities\Folio    as FolioEntity;
use Classes\Entities\Item     as ItemEntity;
use Classes\Entities\ErrorLog as ErrorLogEntity;

use Classes\Helpers\File   as FileHelper;
use Classes\Helpers\Logger;

use Classes\Exceptions\Importer as ImporterException;

class ImportCsvIntoDbTask extends TaskAbstract{


	/* @var CsvRowToFolioEntity */
	private $oCsvRowToFolioEntityMapper;

	private $sCsvFilePath;

	private $sImageImportPath;

	private $sTokenSeperator;

	private $sBoxPrefix;

	private $sItemRegex;

	/* @var BoxEntity */
	private $oBoxEntity;

	/* @var FolioEntity */
	private $oFolioEntity;

	/* @var ItemEntity */
	private $oItemEntity;

	/* @var ErrorLogEntity */
	private $oErrorLogEntity;

	private $iCurrentFolioId;

	private $aScannedFileNames = array();

	public function __construct(  Di				  $oDi
								, CsvRowToFolioEntity $oCsvRowToFolioEntityMapper
								,                     $aConfig
								, JobQueueEntity      $oJobQueueEntity ){

		parent::__construct( $oDi );

		$this->oCsvRowToFolioEntityMapper = $oCsvRowToFolioEntityMapper;

		$this->sCsvFilePath     = $aConfig[ 'path.csv.import' ];

		$this->sImageImportPath = $aConfig[ 'path.image.import' ];

		$this->sTokenSeperator  = $aConfig[ 'tokenseperator' ];

		$this->sBoxPrefix       = $aConfig[ 'box.prefix' ];

		$this->sItemRegex       = $aConfig[ 'item.regex' ];

		$this->iJobQueueId      = $oJobQueueEntity->getId();

		$this->oBoxEntity       = new BoxEntity();

		$this->oFolioEntity     = new FolioEntity();

		$this->oItemEntity      = new ItemEntity();

		$this->sProcess         = 'import';

		$this->iJobQueueId      = $oJobQueueEntity->getId();

        $this->oLogger->SetContext( 'jobs', $this->iJobQueueId );

	}

	/*
	 *
	 */
	public function Execute(){

		// No started flag is needed because we do not have the records to flag!

		try {
			$sFilePath = $this->sCsvFilePath;
			$hHandle   = $this->oFile->GetFileHandle( $sFilePath );

			$this->IterateCsvFileAndInsertRowsIntoDb( $hHandle );

		} catch (Exception $oException ) {
			$this->HandleError( $oException, $oJobQueueEntity );
		}



	}




	/*
	 *
	 */
	private function IterateCsvFileAndInsertRowsIntoDb( $hHandle ){

		$iCurrentRow = 1;

		$aAssocRow   = array();

		$iJobQueueId = $this->iJobQueueId;

		$oBoxEntity  = $this->oBoxEntity;

		$sBoxNumber  = NULL;

		$iBoxId      = NULL;

		while ( $aRow = fgetcsv ( $hHandle, 4000, '|' ) ){

			$iNumberOfFields = count( $aRow );

			// Skip carriage returns
			if( $iNumberOfFields < 2){
				continue;
			}

			// Create an associative array

			// First row provides the array keys
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

			// Do some validation so we skip records with appended letters e.g 098a
			// TODO Need to differentiate between invalid records and faulty ones

			if( preg_match( '/^\d\d\d\z/', $aAssocRow[ 'Box number' ] ) === 0 ){
				continue;
			}

			if( preg_match( '/^\d\d\d\z/', $aAssocRow[ 'Folio number' ] ) === 0 ){
				continue;
			}

			// New Box so insert it

			if( $sBoxNumber != $aAssocRow[ 'Box number' ] ){

				$sBoxNumber = $aAssocRow[ 'Box number' ];

				// Add file names to the Box Item List array

				$this->GetBoxItemList( $sBoxNumber );

				try {
					$oBoxEntity = $this->InsertBox( $sBoxNumber );

				} catch ( ImporterException $oException ) {
					$this->HandleError( $oException, $oBoxEntity );
				}

			}

			$iCurrentRow++;

			// Insert Folio Items

			try {

				$oMappedFolioEntity = $this->InsertFolio( $oBoxEntity, $aAssocRow );

				// If false then the $oMappedFolioEntityrecord already exists so exit
				// TODO Could items be subsequently added to a portfolio?

				if( $oMappedFolioEntity !== false ){
					$this->InsertItems( $oBoxEntity, $oMappedFolioEntity );
				}

			} catch ( ImporterException $oException ) {

				// Escalate error to the Box
				$this->HandleError( $oException, $oMappedFolioEntity );

			}

			// If we have reached this point then all available folios in this box have been completed
			// TODO Update a process log

			$iBoxId = $oBoxEntity->getId();

			$this->oBoxDb->UpdateProcessStatus( $iBoxId, 'import', 'completed' );

		}

	}


	/*
	 * @return BoxEntity
	 */
	private function InsertBox( $sBoxNumber ){

		$iJobQueueId = $this->iJobQueueId;

		$oBoxEntity = $this->oBoxEntity;

		$oBoxEntity->SetAllPropertiesToNull();

		$oBoxEntity->setJobQueueId( $iJobQueueId );

		$oBoxEntity->setBoxNumber( $sBoxNumber );

		$this->oLogger->Log ( 'Inserting Box ' . $sBoxNumber );

		// Returns the new BoxId or the existing one if already there

		$oBoxEntity = $this->oBoxDb->Insert( $oBoxEntity );

		return $oBoxEntity;
	}



	/*
	 *
	 */
	private function InsertFolio( BoxEntity $oBoxEntity, $aAssocRow ){

		$iBoxId = $oBoxEntity->getId();

		$oCsvRowToFolioMapper = $this->oCsvRowToFolioEntityMapper;

		$oMappedFolioEntity   = $oCsvRowToFolioMapper->Map( $aAssocRow );

		if( $oMappedFolioEntity instanceof FolioEntity === false ){
			throw new ImporterException( '$oMappedFolioEntity returned from mapper is not an instance of FolioEntity' );
		}

		$oMappedFolioEntity->setBoxId( $iBoxId );

		/* @var $oMappedFolioEntity FolioEntity */
		$oMappedFolioEntity = $this->oFolioDb->Insert( $oMappedFolioEntity );

		if( $oMappedFolioEntity->getId() === $this->iCurrentFolioId ){
			return false;
		}

		$this->iCurrentFolioId =  $oMappedFolioEntity->getId();

		$this->oLogger->Log ( 'Inserting Folio ' . $oMappedFolioEntity->getFolioNumber() );

		return $oMappedFolioEntity;

	}




	/*
	 *
	 */
	private function InsertItems( BoxEntity $oBoxEntity, FolioEntity $oMappedFolioEntity ){

		$oItemEntity    = $this->oItemEntity;

		$iFolioId       = $oMappedFolioEntity->getId();

		$oItemEntity->setFolioId( $iFolioId );

		$aFolioItemList = $this->GetFolioItemList( $oBoxEntity, $oMappedFolioEntity );


		foreach ( $aFolioItemList as $sFolioItemNumber ){

			$oItemEntity->setItemNumber( $sFolioItemNumber );

			$this->oLogger->Log ( 'Inserting Item ' . $sFolioItemNumber );

			$this->oLogger->Log ( '-----------------------------------' );

			try {
				$this->oItemDb->Insert( $oItemEntity );

			} catch ( ImporterException $oException ) {

				$this->HandleError( $oException, $oItemEntity );

			}

		}

		// If we have reached this point then flag this folio as completed

		$this->oFolioDb->UpdateProcessStatus( $iFolioId, 'import', 'completed' );

	}

	private function GetFolioItemList( BoxEntity $oBoxEntity, FolioEntity $oFolioEntity ){

		$sBoxNumber       = $oBoxEntity->getBoxNumber();
		$sFolioNumber	  = $oFolioEntity->getFolioNumber();
		$sTokenSeperator  = $this->sTokenSeperator;
		$sItemRegex       = $this->sItemRegex;
		$aFileNames       = $this->aScannedFileNames[ $sBoxNumber ];

		$aFolioItemList = array();

		foreach ( $aFileNames as $sFileName ){

				$aTokens             = explode( $sTokenSeperator, $sFileName );

				if( $aTokens === false ){
					throw new ImporterException( 'GetFolioItemList() did not return any tokens for $sFileName: ' . $sFileName . 'in box: ' . $sBoxNumber . ', folio:' . $sFolioNumber );
				}

				$sExpr  = '/' . $sBoxNumber . $sTokenSeperator . $sFolioNumber . $sTokenSeperator . $sItemRegex . '/';

				$iMatch = preg_match( $sExpr , $sFileName, $matches );

				if( $iMatch === 1 ){
					$sItemNumber      = $matches[1];
					$aFolioItemList[] = $sItemNumber;
				}
		}

		return $aFolioItemList;

	}

	private function GetBoxItemList( $sBoxNumber ){

		$sImageImportPath = $this->sImageImportPath;

		$sBoxPrefix       = $this->sBoxPrefix;

		$sBoxDirectory    = $sImageImportPath . DIRECTORY_SEPARATOR . $sBoxPrefix . $sBoxNumber;

		$aFileNames       = scandir( $sBoxDirectory );

		foreach ( $aFileNames as $sFileName ){

			if ( $sFileName != '.' && $sFileName != '..'){

				$this->aScannedFileNames[ $sBoxNumber ][] = $sFileName;

			}

		}


	}


}










































