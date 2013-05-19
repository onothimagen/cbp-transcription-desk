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

use Classes\Db\Box   as BoxDb;
use Classes\Db\Folio as FolioDb;
use Classes\Db\Item  as ItemDb;

use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Box      as BoxEntity;
use Classes\Entities\Folio    as FolioEntity;
use Classes\Entities\Item     as ItemEntity;

use Classes\Helpers\File      as FileHelper;
use Classes\Helpers\Logger;

use Classes\Exceptions\Importer as ImporterException;

/*
 * This drills down from the job, box, folio and item, creates a file name and slices it
 * using methods in the TaskAbstract
 *
 */
class SliceImagesTask  extends TaskAbstract{

	private $sBoxPrefix;

	/* @var $oFolioItemEntity FolioEntity */
	private $oFolioItemEntity;

	private $sSlicerPath;

	private $sTokenSeperator;

	private $sImageImportPath;
	private $sImageExportPath;

	private $sArchivePath;


	/*
	 * @param Di                $oDi
	 * @param string[]          $aConfig
	 * @param JobQueueEntity    $oJobQueueEntity
	 * @return void
	 */
	public function __construct(  Di             $oDi
								,                $aConfig
								, JobQueueEntity $oJobQueueEntity ){

		parent::__construct( $oDi );

		$this->sBoxPrefix       = $aConfig[ 'box.prefix' ];
		$this->sImageImportPath = $aConfig[ 'path.image.import' ];
		$this->sImageExportPath = $aConfig[ 'path.image.export' ];
		$this->sArchivePath     = $aConfig[ 'path.archive' ];
		$this->sSlicerPath      = $aConfig[ 'path.slicer'];
		$this->sTokenSeperator  = $aConfig[ 'tokenseperator'];

		$this->oJobQueueEntity  = $oJobQueueEntity;

		$this->iJobQueueId      = $oJobQueueEntity->getId();

		$this->sProcess         = 'slice';

		$this->sPreviousProcess = 'import';

        $this->oLogger->ConfigureLogger( 'jobs', $this->iJobQueueId );

	}



	/*
	 * Entry point to start task
	 *
	 * @return void
	 */
	public function Execute(){

		// Pre-check paths

		try {
			$sProcess    = $this->sProcess;
			$iJobQueueId = $this->iJobQueueId;
			$this->oBoxDb->FlagJobProcessAsStarted( $iJobQueueId, $sProcess );
			$this->CheckPaths();

			$this->ProcessBoxes( $this->oJobQueueEntity  );
			$this->oBoxDb->FlagJobProcessAsCompleted( $iJobQueueId, $sProcess );

		} catch ( ImporterException $oException ) {
			$this->HandleError( $oException, $this->oJobQueueEntity );
		}

	}


	/*
	 *
	 * @return void
	*/
	private function CheckPaths(){

		$this->oFile->CheckExists( 'ImageImportPath', $this->sImageImportPath );
		$this->oFile->CheckDirExists( $this->sImageExportPath );
		$this->oFile->CheckDirExists( $this->sArchivePath );
		$this->oFile->CheckExists( 'SlicerPath', $this->sSlicerPath );
	}



	/*
	 * Returns a full image path for the supplied box, folio and item
	 * It checks whether the image has already been processed and that it exists
	 *
	 * @return string
	 */
	protected function ConstructPath( BoxEntity   $oBoxEntity
									, FolioEntity $oFolioEntity
								    , ItemEntity  $oItemEntity ){

		$sBoxNumber        = $oBoxEntity->getBoxNumber();

		$sFolioNumber      = $oFolioEntity->getFolioNumber();

		$sItemNumber       = $oItemEntity->getItemNumber();

		$sBoxPrefix        = $this->sBoxPrefix;

		$sImageExportPath  = $this->sImageExportPath;

		$sImageImportPath = $this->sImageImportPath;

		$sJobArchivePath  = $this->sArchivePath;

		$sTokenSeperator = $this->sTokenSeperator;

		$iJobQueueId     = $this->iJobQueueId;


		// Check whether slices already exixt

		$sImagePath  = $sBoxNumber . DIRECTORY_SEPARATOR . $sBoxNumber . $sTokenSeperator . $sFolioNumber  . $sTokenSeperator . $sItemNumber;

		// Check to see whether images have already been processes

		$sImageExportPath  = $this->sImageExportPath;

		$sTargetPath       = $sImageExportPath . DIRECTORY_SEPARATOR . $sImagePath;

		$bTargetPathExists = file_exists( $sTargetPath );

		if( $bTargetPathExists ){
			$this->oLogger->Log( $sTargetPath . ' already exists. Skipping...' );
			return '';
		}


		// They have not been processed so carry on and determine the images full path

		$sImageImportPath = $this->sImageImportPath;

		$this->oFile->CheckDirExists( $sImageImportPath );

		$sBoxPrefix             = $this->sBoxPrefix;

		$sItemName          = DIRECTORY_SEPARATOR . $sBoxPrefix . $sImagePath . '.jpg';

		$sFullImageImportPath   = $sImageImportPath . $sItemName;


		// Fall back to Archive directory if needed

		if( file_exists( $sFullImageImportPath ) === false ){

			$sFullImageArchivePath   = $sJobArchivePath . DIRECTORY_SEPARATOR . $iJobQueueId . $sItemName;

			$this->oLogger->Log( $sFullImageImportPath . ' no longer exists. Now checking archive ' . $sFullImageArchivePath );

			if( file_exists( $sFullImageArchivePath ) ){

				//Before we move file, create the parent BOX directory otherwise it will complain

				$sTargetImageDirectory = $this->sImageImportPath . DIRECTORY_SEPARATOR . $sBoxPrefix . $sBoxNumber;

				if( !is_dir( $sTargetImageDirectory ) ) {

					if( !mkdir( $sTargetImageDirectory, 0775, true )) {
						throw new ImporterException( 'ConstructPath(): Failed to create ' . $sTargetImageDirectory );
					}

					$this->oLogger->Log( 'Created directory ' . $sTargetImageDirectory );
				}

				$this->oLogger->Log( 'Moving archived image back from ' . $sFullImageArchivePath . ' to ' .  $sFullImageImportPath );

				if( !rename( $sFullImageArchivePath, $sFullImageImportPath ) ) {
					throw new ImporterException( 'ConstructPath(): unable to rename ' . $sFullImageArchivePath . ' to ' . $sFullImageImportPath );
				}

			}

			// Otherwise move it back from the archive
		}


		// Do one last check

		$bDirExists = file_exists( $sFullImageImportPath );

		// If it doesn't exist in the Archive directory there then we have a genuine problem so throw an exception
		if( $bDirExists === false ){
			throw new ImporterException( 'FullImageImportPath ' . $sFullImageImportPath . ' does not exist' );
		}

		return $sFullImageImportPath;

	}

	/*
	 * Perform the actual slice operation. See TaskAbstract.
	 *
	 * @param string $sInputImagePath
	 * @return void
	 */
	protected function Process( $sInputImagePath ){

		if( $sInputImagePath == '' ){

			// Further processing skipped

			return;
		}

		$sImageExportPath = $this->sImageExportPath;

		$sSlicerPath      = $this->sSlicerPath;

		$this->oFile->CheckExists( 'ImagePath', $sInputImagePath );

		$this->oFile->CheckDirExists( $sImageExportPath );

		$this->oFile->CheckExists( 'SlicerPath', $sSlicerPath );

		$sCommand = 'perl ' . $sSlicerPath . ' --input_file ' . $sInputImagePath . ' --output_path ' . $sImageExportPath;

		$sCommand = str_replace('\\', '/', $sCommand );

		$sCommand = escapeshellcmd( $sCommand );

		$this->oLogger->Log( 'Executing command: ' . $sCommand );

		$sPerlOutput = '';

		ob_start();
		system( $sCommand );
		$sPerlOutput = ob_get_contents();
		ob_end_clean();

		// Strip out verbose statements and excess carriage returns in Windows
		if( $this->oFile->ServerOS() == 'WIN' ){
			$sPerlOutput = str_replace( "        1 file(s) moved.\r\n", '', $sPerlOutput );
		}

		//$sPerlOutput = 'error';

		if( strpos( strtolower( $sPerlOutput ), 'error' ) !== false ){
			throw new ImporterException( 'Slicer failed when executing command ' . $sCommand . ', Output returned: ' . $sPerlOutput );
		}

		$this->oLogger->Log( $sPerlOutput );

	}


}
































