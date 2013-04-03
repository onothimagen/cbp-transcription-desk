<?php

namespace Classes;

use Zend\Di\Di;

use Classes\Db\JobQueue as JobQueueDb;
use Classes\Db\MetaData as MetaDataDb;
use Classes\Db\Item     as ItemDb;

use Classes\Helpers\File;
use Classes\Helpers\Logger;

use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\MetaData as MetaDataEntity;
use Classes\Entities\Item     as ItemEntity;
use Classes\Helpers\MwXml;

abstract class TaskAbstract{

	/* @var Di */
	protected $oDi;

	/* @var JobQueueDb */
	protected $oJobQueueDb;

	/* @var MetaDataDb */
	protected $oMetaDataDb;

	/* @var ItemDb */
	protected $oItemDb;

	/* @var File */
	protected $oFile;

	/* @var Logger */
	protected $oLogger;

	protected function __construct( Di $oDi ){

		$this->oJobQueueDb = $oDi->get( 'Classes\Db\JobQueue' );
		$this->oMetaDataDb = $oDi->get( 'Classes\Db\MetaData' );

		/* @var ItemDb */
		$this->oItemDb     = $oDi->get( 'Classes\Db\Item' );

		$this->oFile       = $oDi->get( 'Classes\Helpers\File' );
		$this->oLogger     = $oDi->get( 'Classes\Helpers\Logger' );

	}

	/*
	 *
	*/
	protected function PseudoSetterForAutoComplete(
												  JobQueueDb     $oJobQueueDb
												, MetaDataDb     $oMetaDataDb
												, ItemDb         $oItemDb
												, File           $oFile
												, Logger         $oLogger
												, MetaDataEntity $oMetaDataEntity
												, ItemEntity     $oItemEntity
												){

		$this->oJobQueueDb = $oJobQueueDb;
		$this->oMetaDataDb = $oMetaDataDb;
		$this->oItemDb 	   = $oItemDb;

		$this->oFile   = $oFile;
		$this->oLogger = $oLogger;

		$this->oMetaDataEntity = $oMetaDataEntity;
		$this->oItemEntity     = $oItemEntity;


	}
}







































