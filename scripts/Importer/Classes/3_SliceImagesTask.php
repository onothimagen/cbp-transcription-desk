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

use Classes\Helpers\File   as FileHelper;

use Classes\Exceptions\Importer as ImporterException;

class SliceImagesTask  extends TaskAbstract{

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

		$this->sImageImportPath = $aConfig[ 'path.image.import' ];
		$this->sImageExportPath = $aConfig[ 'path.image.export' ];
		$this->sArchivePath     = $aConfig[ 'path.archive' ];
		$this->sSlicerPath      = $aConfig[ 'path.slicer'];

		$this->iJobQueueId      = $oJobQueueEntity->getId();

		$this->sProcess         = 'slice';

		$this->sPreviousProcess = 'import';

	}



	/*
	 *
	 */
	public function Execute(){

		// Pre-check paths

		try {
			$this->CheckPaths();

			// Don't flag all entities as as started. This is done in a granular way for this process.

			$this->ProcessBoxes();

		} catch (Exception $oException) {
			$this->HandleError( $oException, $oJobQueueEntity );
		}

	}


	/*
	 *
	*/
	private function CheckPaths(){

		$this->oFile->CheckExists( 'ImageImportPath', $this->sImageImportPath );
		$this->oFile->CheckExists( 'ImageExportPath', $this->sImageExportPath );
		$this->oFile->CheckExists( 'ArchivePath', $this->sArchivePath );
		$this->oFile->CheckExists( 'SlicerPath', $this->sSlicerPath );

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

		$sImageImportPath = $this->sImageImportPath;

		$this->oFile->CheckExists( 'ImageImportPath', $sImageImportPath );

		$sRootPath      = $this->sImageImportPath;

		$sFullImagePath = $sRootPath . DIRECTORY_SEPARATOR . $sImagePath . '.jpg';

		return $sFullImagePath;

	}

	/*
	 *
	 */
	protected function Process( $sImagePath ){

		$sImageExportPath = $this->sImageExportPath;

		$sSlicerPath      = $this->sSlicerPath;

		// Just in time checks

		$this->oFile->CheckExists( 'ImagePath', $sImagePath );

		$this->oFile->CheckExists( 'ImageExportPath', $sImageExportPath );

		$this->oFile->CheckExists( 'SlicerPath', $sSlicerPath );

		$sCommand       = 'perl ' . $sSlicerPath . ' --input_file ' . $sImagePath . ' --output_path ' . $this->sImageExportPath;

		$sCommand       = str_replace('\\', '/', $sCommand );

		ob_start();
		passthru( $sCommand );
		$sPerlOutput = ob_get_contents();
		ob_end_clean();

		// Strip out verbose statements and excess carriage returns in Windows
		if( $this->oFile->ServerOS() == 'WIN' ){
			$sPerlOutput = str_replace( "        1 file(s) moved.\r\n", '', $sPerlOutput );
		}

		if( strpos( strtolower( $sPerlOutput ), 'Error' ) === true ){
			throw new ImporterException( 'Slicer failed when executing command ' . $sCommand . ', Output returned: ' . $sPerlOutput );
		}

		echo $sPerlOutput . '<p />';
	}


}
































