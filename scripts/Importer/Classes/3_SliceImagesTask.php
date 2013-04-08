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

use Classes\Entities\Box   as BoxEntity;
use Classes\Entities\Folio as FolioEntity;
use Classes\Entities\Item  as ItemEntity;

use Classes\Helpers\File   as FileHelper;

class SliceImagesTask  extends TaskAbstract{

	/* @var $oFolioItemEntity FolioEntity */
	private $oFolioItemEntity;

	private $sSlicerPath;

	private $sImageImportPath;
	private $sImageExportPath;

	private $sArchivePath;

	private $iJobQueueId;


	public function __construct( Di $oDi
								,   $aSectionConfig
								,   $iJobQueueId ){

		parent::__construct( $oDi );

		$this->sImageImportPath = $aSectionConfig[ 'path.image.import' ];
		$this->sImageExportPath = $aSectionConfig[ 'path.image.export' ];
		$this->sArchivePath     = $aSectionConfig[ 'path.archive' ];
		$this->sSlicerPath      = $aSectionConfig[ 'path.slicer'];

		$this->iJobQueueId      = $iJobQueueId;

		$this->sProcess         = 'slice';

	}



	/*
	 *
	 */
	public function Execute(){

		$this->CheckPaths();

		$this->SliceBoxes();

	}

	/*
	 *
	 */
	private function CheckPaths(){

		$this->oFile->CheckExists( $this->sImageImportPath );
		$this->oFile->CheckExists( $this->sImageExportPath );
		$this->oFile->CheckExists( $this->sArchivePath );
		$this->oFile->CheckExists( $this->sSlicerPath );

	}




	/*
	 *
	 */
	private function SliceBoxes(){

		$rBoxes = $this->GetBoxesToSlice();

		/* @var $oBoxEntity BoxEntity */
		while ( $oBoxEntity = $rBoxes->getResource()->fetchObject( 'Classes\Entities\Box' ) ){

			$iBoxId = $oBoxEntity->getId();

			$this->oBoxDb->UpdateProcessStatus( $iBoxId, 'slice', 'started' );

			try {
				$this->SliceFolios( $oBoxEntity );
			} catch ( ImporterException $oException ) {
				$this->HandleError( $oException, $oBoxEntity );
			}

			$this->oBoxDb->UpdateProcessStatus( $iBoxId, 'slice', 'completed' );

		}

	}


	/*
	 *
	 */

	private function SliceFolios( BoxEntity $oBoxEntity ){

		$iBoxId = $oBoxEntity->getId();

		$rFolios = $this->GetFoliosToSlice( $iBoxId );

		/* @var $oFolioEntity FolioEntity */
		while ( $oFolioEntity = $rFolios->getResource()->fetchObject( 'Classes\Entities\Folio' ) ){

			$iFolioId = $oFolioEntity->getId();

			$this->oFolioDb->UpdateProcessStatus( $iFolioId, 'slice', 'started' );

			try {
				$this->SliceItems( $oBoxEntity, $oFolioEntity );
			} catch ( ImporterException $oException ) {
				$this->HandleError( $oException, $oFolioEntity );
			}

			$this->oFolioDb->UpdateProcessStatus( $iFolioId, 'slice', 'completed' );

		}

	}


	/*
	 *
	*/

	private function SliceItems( BoxEntity $oBoxEntity, FolioEntity $oFolioEntity ){

		$iFolioId = $oFolioEntity->getId();

		$rItems = $this->GetItemsToSlice( $iFolioId );

		/* @var $oItemEntity ItemEntity */
		while ( $oItemEntity = $rItems->getResource()->fetchObject( 'Classes\Entities\Item' ) ){

			$iItemId = $oItemEntity->getId();

			$this->oItemDb->UpdateProcessStatus(
													$iItemId
													, 'slice'
													, 'started'
													);


			try {
				$sImagePath = $this->ConstructImagePath( $oBoxEntity, $oFolioEntity, $oItemEntity );
				$this->SliceImage( $sImagePath );

			} catch ( ImporterException $oException ) {
				$this->HandleError( $oException, $oItemEntity );
			}

			$this->oItemDb->UpdateProcessStatus(
													$iItemId
													, 'slice'
													, 'completed'
													);
		}



	}





	private function ConstructImagePath(
										  BoxEntity $oBoxEntity
										, FolioEntity $oFolioEntity
										, ItemEntity $oItemEntity ){

		$sBoxNumber   = $oBoxEntity->getBoxNumber();

		$sFolioNumber = $oFolioEntity->getFolioNumber();

		$sItemNumber  = $oItemEntity->getItemNumber();

		$sImagePath  = $sBoxNumber . DIRECTORY_SEPARATOR . $sBoxNumber . '_' . $sFolioNumber  . '_' . $sItemNumber;

		$sRootPath      = $this->sImageImportPath;

		$sFullImagePath = $sRootPath . DIRECTORY_SEPARATOR . $sImagePath . '.jpg';

		if( file_exists( $sFullImagePath ) === false ){
			throw new \Classes\Exceptions\Importer( $sFullImagePath . ' passed to SliceImage() does not exist' );
		}

		return $sFullImagePath;

	}

	/*
	 *
	 */
	private function SliceImage( $sImagePath ){

		$sSlicerPath    = $this->sSlicerPath;

		$sCommand       = 'perl ' . $sSlicerPath . ' --input_file ' . $sImagePath . ' --output_path ' . $this->sImageExportPath;

		$sCommand       = str_replace('\\', '/', $sCommand );

		//echo $sCommand . '< p />';

		ob_start();
		passthru( $sCommand );
		$perlreturn = ob_get_contents();
		ob_end_clean();

		//echo $perlreturn;

	}

	/*
	 *
	 */
	private function GetBoxesToSlice(){

		return $this->oBoxDb->GetBoxes(
										  $this->iJobQueueId
										, 'import'
										, 'completed'
										  );
	}


	/*
	 *
	 */
	private function GetFoliosToSlice( $iBoxId ){

		return $this->oFolioDb->GetFolios(
										  $iBoxId
										, 'import'
										, 'completed'
										  );
	}

	/*
	 *
	 */
	private function GetItemsToSlice( $iFolioId ){

		return $this->oItemDb->GetJobItems(
											$iFolioId
										 , 'import'
										 , 'completed'
										  );
	}



}
































