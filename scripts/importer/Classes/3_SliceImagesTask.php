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

class SliceImagesTask  extends TaskAbstract{

	private $sBoxPrefix;

	/* @var $oFolioItemEntity FolioEntity */
	private $oFolioItemEntity;

	private $sSlicerPath;

	private $sImageImportPath;
	private $sImageExportPath;

	private $sArchivePath;

	public function __construct(  Di             $oDi
								,                $aConfig
								, JobQueueEntity $oJobQueueEntity ){

		parent::__construct( $oDi );

		$this->sBoxPrefix       = $aConfig[ 'box.prefix' ];
		$this->sImageImportPath = $aConfig[ 'path.image.import' ];
		$this->sImageExportPath = $aConfig[ 'path.image.export' ];
		$this->sArchivePath     = $aConfig[ 'path.archive' ];
		$this->sSlicerPath      = $aConfig[ 'path.slicer'];

		$this->oJobQueueEntity  = $oJobQueueEntity;

		$this->iJobQueueId      = $oJobQueueEntity->getId();

		$this->sProcess         = 'slice';

		$this->sPreviousProcess = 'import';

        $this->oLogger->SetContext( 'jobs', $this->iJobQueueId );

	}



	/*
	 *
	 */
	public function Execute(){

		// Pre-check paths

		try {
			$this->CheckPaths();

			// Don't flag all entities as started. This is done in a granular way for this process.

			$this->ProcessBoxes();

		} catch ( ImporterException $oException ) {
			$this->HandleError( $oException, $this->oJobQueueEntity );
		}

	}


	/*
	 *
	*/
	private function CheckPaths(){

		$this->oFile->CheckDirExists( 'ImageImportPath', $this->sImageImportPath );
		$this->oFile->CheckDirExists( 'ImageExportPath', $this->sImageExportPath );
		$this->oFile->CheckDirExists( 'ArchivePath', $this->sArchivePath );
		$this->oFile->CheckDirExists( 'SlicerPath', $this->sSlicerPath );

	}

	/*
	 *
	 */
	protected function ConstructPath(
									  BoxEntity   $oBoxEntity
									, FolioEntity $oFolioEntity
									, ItemEntity  $oItemEntity ){

		$sBoxNumber   = $oBoxEntity->getBoxNumber();

		$sFolioNumber = $oFolioEntity->getFolioNumber();

		$sItemNumber  = $oItemEntity->getItemNumber();

		$sImagePath  = $sBoxNumber . DIRECTORY_SEPARATOR . $sBoxNumber . '_' . $sFolioNumber  . '_' . $sItemNumber;

		// Check to see whether images have already been processes

		$sImageExportPath  = $this->sImageExportPath;

		$sTargetPath       = $sImageExportPath . DIRECTORY_SEPARATOR . $sImagePath;

		$bTargetPathExists = file_exists( $sTargetPath );

		if( $bTargetPathExists ){
			$this->oLogger->Log( $sTargetPath . ' already exists. Skipping...' );
			return '';
		}

		// It does not exist yet so carry on

		$sImageImportPath = $this->sImageImportPath;

		$this->oFile->CheckDirExists( 'ImageImportPath', $sImageImportPath );

		$sRootPath        = $this->sImageImportPath;

		$sBoxPrefix       = $this->sBoxPrefix;

		$sFullImagePath   = $sRootPath . DIRECTORY_SEPARATOR . $sBoxPrefix . $sImagePath . '.jpg';

		return $sFullImagePath;

	}

	/*
	 *
	 */
	protected function Process( $sImagePath ){

		if( $sImagePath == '' ){
			return false;
		}

		$sImageExportPath = $this->sImageExportPath;

		$sSlicerPath      = $this->sSlicerPath;

		$this->oFile->CheckExists( 'ImagePath', $sImagePath );

		$this->oFile->CheckDirExists( 'ImageExportPath', $sImageExportPath );

		$this->oFile->CheckExists( 'SlicerPath', $sSlicerPath );

		$sCommand = 'perl ' . $sSlicerPath . ' --input_file ' . $sImagePath . ' --output_path ' . $this->sImageExportPath;

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
































