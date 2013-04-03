<?php

namespace Classes;

use Zend\Di\Di;

use Zend\Db\ResultSet\ResultSet;

use Classes\Db\MetaData as MetaDataDb;
use Classes\Db\Item as ItemDb;
use Classes\Entities\MetaData as MetaDataEntity;

class SliceImagesTask  extends TaskAbstract{

	/* @var $oMetaDataItemEntity MetaDataEntity */
	private $oMetaDataItemEntity;

	private $sSlicerPath;

	private $sImageImportPath;
	private $sImageExportPath;

	private $sArchivePath;

	private $iJoBQueueId;


	public function __construct( Di $oDi
								,   $aSectionConfig
								,   $iJobQueueId ){

		parent::__construct( $oDi );

		$this->sImageImportPath = $aSectionConfig[ 'path.image.import' ];
		$this->sImageExportPath = $aSectionConfig[ 'path.image.export' ];
		$this->sArchivePath     = $aSectionConfig[ 'path.archives' ];

		$this->sSlicerPath      = $aSectionConfig[ 'path.slicer'];

		$this->iJobQueueId      = $iJobQueueId;

	}



	/*
	 *
	 */
	public function Execute(){

		$this->SliceImages();

	}

	/*
	 *
	 */
	private function SliceImages( ){

		$rMetaData = $this->GetAllMetaDataItemsToSlice();

		/* @var $oMetaDataItemEntity MetaDataEntity */
		while ( $oMetaDataItemEntity = $rMetaData->getResource()->fetchObject( 'Classes\Entities\MetaData' ) ){

			$iItemId  	  = $oMetaDataItemEntity->getItemId();

			echo $iItemId . '<p />';

			$sImagePath = $this->ConstructImagePath( $oMetaDataItemEntity );

			try {
				//$this->SliceImage( $sImagePath );

				$this->oItemDb->UpdateJobProcessStatusByItemId(
															  $iItemId
															, 'export'
															, 'queued'
															, 'slice'
															, 'queued'
															);
			} catch (Exception $e) {
				// Write to log
			}



		}



		$this->oMetaDataDb->UpdateProcessStatusByJobQueueId(
														  $this->iJobQueueId
														, 'export'
														, 'queued'
														, 'slice'
														, 'queued'
														);

	}

	private function ConstructImagePath( MetaDataEntity $oEntity ){

		$sBoxNumber   = $oEntity->getBoxNumber();

		$sFolioNumber = $oEntity->getFolioNumber();

		$sItemNumber  = $oEntity->getItemNumber();

		$sImagePath  = $sBoxNumber . DIRECTORY_SEPARATOR . $sBoxNumber . '_' . $sFolioNumber  . '_' . $sItemNumber;

		$sRootPath      = $this->sImageImportPath;

		$sFullImagePath = $sRootPath . DIRECTORY_SEPARATOR . $sImagePath . '.jpg';

		if( file_exists( $sFullImagePath ) === false ){
			throw new \Classes\Exceptions\Importer( $sFullImagePath . 'passed to SliceImage() does not exist' );
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
	private function GetAllMetaDataItemsToSlice(){

		return $this->oMetaDataDb->GetJobMetaDataItems(
														  $this->iJobQueueId
														, 'slice'
														, 'queued'
													  );

	}



}
































