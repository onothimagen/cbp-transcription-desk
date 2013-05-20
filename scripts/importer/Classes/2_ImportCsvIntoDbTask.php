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

/*
 * Scan file sytem for new boxes and their images
 * This can be throttled to import only a specified number of boxes at a time
 * Iterate through records in Metatada-Table.txt and create a multi-dimensional array for rows corresponding to the boxes scanned
 * Iterate through the meta data array
 * 	Insert each box
 * 		Insert each folio within the box
 * 			Find matches with the scanned files corresponding to folio
 */

class ImportCsvIntoDbTask extends TaskAbstract{


	/* @var CsvRowToFolioEntity */
	private $oCsvRowToFolioEntityMapper;

	private $sCsvFilePath;

	private $sImageImportPath;

	private $sArchivePath;

	private $sBoxPrefix;

	private $sRegexBox;

	private $sRegexFolio;

	private $sRegexItem;

	private $sRegex;

	private $sBoxLimit;

	private $sTokenSeperator;

	/* @var BoxEntity */
	private $oBoxEntity;

	/* @var FolioEntity */
	private $oFolioEntity;

	/* @var ItemEntity */
	private $oItemEntity;

	/* @var ErrorLogEntity */
	private $oErrorLogEntity;

	private $aScannedFileNames   = array();

	private $aBoxFolioCollection = array();

	/*
	* @param Di                     $oDi
	* @param CsvRowToFolioEntity    $oCsvRowToFolioEntityMapper
	* @param string[]               $aConfig
	* @param JobQueueEntity         $oJobQueueEntity
	* @return void
	*/
	public function __construct(  Di				  $oDi
								, CsvRowToFolioEntity $oCsvRowToFolioEntityMapper
								,                     $aConfig
								, JobQueueEntity      $oJobQueueEntity ){

		parent::__construct( $oDi );

		$this->oCsvRowToFolioEntityMapper = $oCsvRowToFolioEntityMapper;

		$this->sCsvFilePath     = $aConfig[ 'path.csv.import' ];

		$this->sImageImportPath = $aConfig[ 'path.image.import' ];

		$this->sArchivePath     = $aConfig[ 'path.archive' ];

		$this->sBoxPrefix       = $aConfig[ 'box.prefix' ];

		$this->sRegexBox        = $aConfig[ 'regex.box' ];

		$this->sRegexFolio      = $aConfig[ 'regex.folio' ];

		$this->sRegexItem       = $aConfig[ 'regex.item' ];

		$this->sBoxLimit        = $aConfig[ 'import_box_limit' ];

		$this->sTokenSeperator  = $aConfig[ 'tokenseperator'];

		$this->iJobQueueId      = $oJobQueueEntity->getId();

		$this->oBoxEntity       = new BoxEntity();

		$this->oFolioEntity     = new FolioEntity();

		$this->oItemEntity      = new ItemEntity();

		$this->sProcess         = 'import';

		$this->oJobQueueEntity  = $oJobQueueEntity;

		$this->iJobQueueId      = $oJobQueueEntity->getId();

        $this->oLogger->ConfigureLogger( 'jobs', $this->iJobQueueId );

		$this->sRegex  			= '/('  . $this->sRegexBox . ')' . $this->sTokenSeperator . '(' . $this->sRegexFolio . ')'
										. $this->sTokenSeperator  . '(' . $this->sRegexItem  . ')' . '.jpg' . '/';
	}



	/*
	 * Entry point to start task
	 *
	 * @return void
	 */
	public function Execute(){

		// No started flag is needed because we do not have the records to flag!

		try {

			$this->GetJobBoxes();

			$this->GetFoliosFromCsvFile();

			$this->IterateFolioCollectionAndInsertRowsIntoDb();

		} catch ( ImporterException $oException ) {

			$this->HandleError( $oException, $this->oJobQueueEntity );

		}

	}


	/*
	 * Scans upload directory and populates a array $this->aScannedFileNames[ $sBoxNumber ][ $sFolioNumber][] = $sItemNumber;
	 *
	 * @todo perhaps scanning image upload directory should use stricter regex match $sBoxPrefix_\d\d\d
	 * @return string[]
	 */
	private function GetJobBoxes(){

		$sImageImportPath    = $this->sImageImportPath;

		$sArchivePath	     = $this->sArchivePath;

		$sBoxLimit           = $this->sBoxLimit;

		$sBoxPrefix          = $this->sBoxPrefix;

		$iJobQueueId         = $this->iJobQueueId;

		$bHasUserSpecifiedId = $this->HasUserSpecifiedId();

		/* Scan boxes */

		/* If user has specified path, then reprocess boxes */

		if( $bHasUserSpecifiedId ){

			/* Get previously imported boxes */

			$aBoxes = $this->GetBoxCollection();

			/* If no boxes imported fallback to directory re-scan */

			if( count ( $aBoxes ) < 1 ){
				$this->oLogger->Log ( 'Cannot find any boxes in cbp_boxes table, scanning ' . $sImageImportPath );
				$aBoxes = $this->oFile->ScanImageDirectory( $sImageImportPath, $sBoxPrefix );
			}

			/* If no boxes imported fallback to archive re-scan */

			if( count ( $aBoxes ) < 1 ){

				$sArchiveImportPath = $sArchivePath . DIRECTORY_SEPARATOR . $iJobQueueId;

				$this->oLogger->Log ( 'Cannot find any boxes in ' . $sImageImportPath . ', scanning ' . $sArchiveImportPath );

				$sImageImportPath   = $sArchiveImportPath;

				$aBoxes             = $this->oFile->ScanImageDirectory( $sImageImportPath, $sBoxPrefix );
			}

		}else{
			$this->oLogger->Log ( 'Scanning ' . $sImageImportPath );
			$aBoxes = $this->oFile->ScanImageDirectory( $sImageImportPath, $sBoxPrefix );
		}


		if( count ( $aBoxes ) < 1 ){
			throw new ImporterException( 'ScanImageDirectory() could not find any boxes in the image upload folder ' . $sImageImportPath );
		}

		$counter          = 0;

		/* Scan box contents */

		foreach ( $aBoxes as $sBoxNumber ){

			$sBoxDirectory = $sImageImportPath . DIRECTORY_SEPARATOR . $sBoxNumber;

			$aFiles        = $this->oFile->ScanDirectory( $sBoxDirectory, 'file' );

			if( count( $aFiles ) < 1 ){
				throw new ImporterException( 'ScanBoxDirectory() could not find any files in box folder ' . $sBoxNumber );
			}

			$sBoxNumber = str_replace( $sBoxPrefix, '', $sBoxNumber );

			foreach ( $aFiles as $sFileName ){

				$this->oLogger->Log('Processing file ' . $sFileName );

				$aParsedFileName = $this->ParseFileName( $sFileName );

				$sFolioNumber    = $aParsedFileName[ 'folio' ];
				$sItemNumber     = $aParsedFileName[ 'item' ];

				$this->aScannedFileNames[ $sBoxNumber ][ $sFolioNumber][] = $sItemNumber;

			}

			$counter++;

			if( $counter > $sBoxLimit ){
				break;
			}
		}

	}


	/*
	 * Get an array of boxes from the DB for specified job ID
	 *
	 * @return string[]
	 */
	private function GetBoxCollection (){

		$iJobQueueId = $this->iJobQueueId;

		$rBoxes      = $this->GetBoxes( $iJobQueueId );

		$sBoxPrefix  = $this->sBoxPrefix;

		$aBoxes = array();

		/* @var $oBoxEntity BoxEntity */
		while ( $oBoxEntity = $rBoxes->getResource()->fetchObject( 'Classes\Entities\Box' ) ){
			$aBoxes[] = $sBoxPrefix . $oBoxEntity->getBoxNumber();
		}

		return $aBoxes;

	}


	/*
	 * @param string $sFileName
	 * @return string[]
	 */
	private function ParseFileName( $sFileName ){

		$sRegex  = $this->sRegex;

		$iMatch = preg_match( $sRegex, $sFileName, $matches );

		if( $iMatch !== 1 or count($matches) < 4 ){
			throw new ImporterException( $sFileName . ' does not match with configured: ' . $sExpr );
		}

		$aFileTokens = array( 'box'   => $matches[1]
							, 'folio' => $matches[2]
							, 'item'  => $matches[3]
							);

		return $aFileTokens;

	}



	/*
	 * Scans the meta data table and adds new boxes and folios to the respective tables
	* If no new boxes are found then it deletes the job and exits
	*
	* @return array[ string[] ]
	* @todo Could remove any log files created as well
	* @todo This method is too bulky and needs refactoring
	*/
	private function GetFoliosFromCsvFile(){

		$sFilePath           = $this->sCsvFilePath;

		$hHandle             = $this->oFile->GetFileHandle( $sFilePath );

		$sBoxPrefix          = $this->sBoxPrefix;

		$sRegexBox           = $this->sRegexBox;

		$sRegexFolio         = $this->sRegexFolio;

		$iCurrentRow         = 1;

		$aBoxFolioCollection = array();
		$aAssocRow           = array();

		$sBoxLimit           = $this->sBoxLimit;

		$iBoxCount           = 0;

		while ( $aRow = fgetcsv ( $hHandle, 4000, '|' ) ){

			/* Add each row to an associative array */

			$iNumberOfFields = count( $aRow );

			/* Skip carriage returns */
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

			$sBoxNumber   = $aAssocRow[ 'Box number' ];
			$sFolioNumber = $aAssocRow[ 'Folio number' ];

			if( preg_match( '/' . $sRegexBox . '/', $sBoxNumber ) === 0 ){
				continue;
			}

			if( preg_match( '/'. $sRegexFolio . '/', $sFolioNumber ) === 0 ){
				continue;
			}

			/* Is the box in amongst our scanned boxes */

			if( array_key_exists( $sBoxNumber, $this->aScannedFileNames )){
				$this->aBoxFolioCollection[ $sBoxNumber ][$sFolioNumber] = $aAssocRow;
			}
		}
	}

	/*
	 * Scans the meta data table and adds new boxes and folios to the respective tables
	 * If no new boxes are found then it deletes the job and exits
	 *
	 * @return void
	 * @todo Could remove any log files created as well
	 * @todo This method is too bulky and needs refactoring
	 */
	private function IterateFolioCollectionAndInsertRowsIntoDb(){

		$iJobQueueId  = $this->iJobQueueId;

		$oBoxEntity   = $this->oBoxEntity;

		$aBoxFolioCollection = $this->aBoxFolioCollection;

		foreach( $aBoxFolioCollection as $sBoxNumber => $aBoxFolios ){

			try {

				$oBoxEntity = $this->InsertBox( $sBoxNumber );

				/* If Box has already been added then DO NOT skip
				 * Something may have gone wrong previously so we need to
				 * reprocess everything
				 */

				// Delete any entries in the error log because we are starting anew
				$iBoxId = $oBoxEntity->getId();
				$this->oBoxDb->ClearErrorLog( $iBoxId );


				try {

					foreach ( $aBoxFolios as $aBoxFolio ){

						$oFolioEntity = $this->InsertFolio( $oBoxEntity, $aBoxFolio );

						/* If Folio has already been added then DO NOT skip
						 * Something may have gone wrong previously so we need to
						 * reprocess everything
						 */

						// Delete any entries in the error log because we are starting anew
						$iFolioId = $oFolioEntity->getId();
						$this->oFolioDb->ClearErrorLog( $iFolioId );

						$this->ScanAndInsertFolioItems( $oBoxEntity, $oFolioEntity );

					}


				} catch ( ImporterException $oException ) {

					// Escalate error to the parent box
					$this->HandleError( $oException, $oFolioEntity );

				}


			} catch ( ImporterException $oException ) {

				// Escalate error to the parent job
				$this->HandleError( $oException, $oBoxEntity );
			}

		}

		/* Reached the end. Check there are not unmatched files */

		foreach ( $this->aScannedFileNames as $iBox => $aFolios ){
			if( count ( $aFolios ) > 0 ){
				$aFlattenedScannedFileNames = $this->FlattenedScannedFileNames();
				throw new ImporterException( 'The following images could not be matched with meta data ' . $aFlattenedScannedFileNames );
			}
		}
	}


	/*
	 * @return string[]
	 */
	private function FlattenedScannedFileNames(){

		$sFileNameList = '';

		$sTokenSeperator = $this->sTokenSeperator;

		foreach ( $this->aScannedFileNames as $aBoxNumber => $aFolios){

			foreach ( $aFolios as $sFolioNumber => $aItemNumbers ){

				foreach ( $aItemNumbers as $sItemNumber){

					$sFileNameList .= $aBoxNumber . $sTokenSeperator . $sFolioNumber . $sTokenSeperator . $sItemNumber . ', ';
				}
			}

		}

		return $sFileNameList;
	}


	/*
	 * @return BoxEntity|boolean
	 */
	private function InsertBox( $sBoxNumber ){

		$iJobQueueId = $this->iJobQueueId;

		$oBoxEntity = $this->oBoxEntity;

		$oBoxEntity->SetAllPropertiesToNull();

		$oBoxEntity->setJobQueueId( $iJobQueueId );

		$oBoxEntity->setBoxNumber( $sBoxNumber );


		/* Returns the new $oBoxEntity or the existing one if already there */

		/* @var $oBoxEntity  BoxEntity */
		$oBoxEntity = $this->oBoxDb->Insert( $oBoxEntity );

		/* Already exists so updated and not inserted */

		$sBoxUpdated = $oBoxEntity->getUpdated();

		/* We still need to return a BoxEntity because
		 * there will be more than row with that Box number
		 */

		if( $sBoxUpdated === null ){
			$this->oLogger->Log ( 'Box ' . $sBoxNumber . ' already exists' );
		}else{
			$this->oLogger->Log ( 'Box ' . $sBoxNumber . 'has been inserted' );
		}

		return $oBoxEntity;
	}



	/*
	 * Populates the FolioEntity and the inserts it
	 *
	 * @param BoxEntity $oBoxEntity
	 * @param string[] $aAssocRow
	 * @return FolioEntity $oFolioEntity
	 */
	private function InsertFolio( BoxEntity $oBoxEntity, $aBoxFolio ){


		$oCsvRowToFolioMapper = $this->oCsvRowToFolioEntityMapper;

		$oMappedFolioEntity   = $oCsvRowToFolioMapper->Map( $aBoxFolio );

		if( $oMappedFolioEntity instanceof FolioEntity === false ){
			throw new ImporterException( '$oMappedFolioEntity returned from mapper is not an instance of FolioEntity' );
		}

		$iBoxId = $oBoxEntity->getId();

		$oMappedFolioEntity->setBoxId( $iBoxId );

		/* @var $oMappedFolioEntity FolioEntity */
		$oMappedFolioEntity = $this->oFolioDb->Insert( $oMappedFolioEntity );

		/* Already exists so updated and not inserted */

		$sFolioUpdated = $oMappedFolioEntity->getUpdated();

		$sFolioNumber  = $oMappedFolioEntity->getFolioNumber();

		if( $sFolioUpdated === null ){
			$this->oLogger->Log ( 'Folio ' . $sFolioNumber . ' already exists' );
		}else{
			$this->oLogger->Log ( 'Inserting Folio ' . $oMappedFolioEntity->getFolioNumber() );
		}

		return $oMappedFolioEntity;

	}




	/*
	 * Loops through the scanned items found e.g. 001, 002 for the specified box and folio
	 * and inserts them into cbp_items
	 *
	 * @param BoxEntity $oBoxEntity
	 * @param FolioEntity $oFolioEntity
	 * @return void
	 */
	private function ScanAndInsertFolioItems( BoxEntity $oBoxEntity, FolioEntity $oMappedFolioEntity ){

		$sBoxNumber           = $oBoxEntity->getBoxNumber();
		$sFolioNumber         = $oMappedFolioEntity->getFolioNumber();

		$aScannedItemNumbers  = $this->aScannedFileNames[ $sBoxNumber ][ $sFolioNumber];

		if( $aScannedItemNumbers < 1 ){
			throw new ImporterException( 'ScanAndInsertFolioItems() could find no corresponding files in $this->aScannedFileNames for box number ' . $sBoxNumber . ' and folio number ' . $sFolioNumber );
		}

		$oItemEntity = $this->oItemEntity;

		foreach ( $aScannedItemNumbers as $sItemNumber ){

			$oItemEntity->SetAllPropertiesToNull();

			$oItemEntity->setItemNumber( $sItemNumber );

			$iFolioId     = $oMappedFolioEntity->getId();

			$oItemEntity->setFolioId( $iFolioId );

			$oItemEntity  = $this->oItemDb->Insert( $oItemEntity );

			/* Already exists so updated and not inserted */

			$sItemUpdated = $oItemEntity->getUpdated();

			if( $sItemUpdated === null ){
				$this->oLogger->Log ( 'Item ' . $sItemNumber . ' already exists' );
				continue;
			}

			$this->oLogger->Log ( 'Inserted Item ' . $sItemNumber );

		}

		/* Remove the item from the array so reduce the size */

		unset( $this->aScannedFileNames[ $sBoxNumber ][ $sFolioNumber] );

		unset( $this->aBoxFolioCollection[ $sBoxNumber ][$sFolioNumber] );

		// If we have reached this point then flag this folio as completed

		$this->oFolioDb->UpdateProcessStatus( $iFolioId, 'import', 'completed' );

		$this->oFolioDb->ClearErrorLog( $iFolioId );

		$this->oLogger->Log ( '-----------------------------------' );

	}

	/*
	 * @return boolean
	 */
    private function HasUserSpecifiedId(){
    	global $argv;

        if( isset( $_GET[ 'job_id' ] ) or isset( $argv[ 1 ] ) ) {
            return true;
        }
        return false;
    }


}











































