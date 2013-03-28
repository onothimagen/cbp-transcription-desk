<?php

namespace Classes;

use Zend\Di\Di;

use Zend\Db\ResultSet\ResultSet;

use Classes\Entities\MetaDataItem as MetaDataItemEntity;

class SliceImagesTask  extends TaskAbstract{

	/* @var $oMetaDataItemEntity MetaDataItemEntity */
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

		/* @var $oMetaDataItemEntity MetaDataItemEntity */
		while ( $oMetaDataItemEntity = $rMetaData->getResource()->fetchObject( 'Classes\Entities\MetaDataItem' ) ){

			$iMetaDataId  = $oMetaDataItemEntity->getItemId();

			$sBoxNumber   = $oMetaDataItemEntity->getBoxNumber();

			$sFolioNumber = $oMetaDataItemEntity->getFolioNumber();

			$sItemNumber  = $oMetaDataItemEntity->getItemNumber();

			$sImagePath  = $sBoxNumber . DIRECTORY_SEPARATOR . $sBoxNumber . '_' . $sFolioNumber  . '_' . $sItemNumber;

			$this->SliceImage( $sImagePath );

		}

	}

	/*
	 *
	 */
	private function SliceImage( $sImagePath ){

		$sRootPath      = $this->sImageImportPath;

		$sFullImagePath = $sRootPath . DIRECTORY_SEPARATOR . $sImagePath;

		$sSlicerPath    = $this->sSlicerPath;

		$sCommand       = 'perl ' . $sSlicerPath . ' --input_file ' . $sFullImagePath . '.jpg --output_path ' . $this->sImageExportPath;

		$sCommand       = str_replace('\\', '/', $sCommand );

		echo $sCommand . '< p >';

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
































