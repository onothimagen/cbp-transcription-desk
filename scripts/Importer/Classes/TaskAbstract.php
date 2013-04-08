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

use Classes\Db\JobQueue as JobQueueDb;
use Classes\Db\Box      as BoxDb;
use Classes\Db\Folio    as FolioDb;
use Classes\Db\Item     as ItemDb;
use Classes\Db\ErrorLogDb as ErrorLogDb;

use Classes\Helpers\File   as FileHelper;
use Classes\Helpers\Logger as Logger;

use Classes\Entities\JobQueue         as JobQueueEntity;
use Classes\Entities\Box             as BoxEntity;
use Classes\Entities\Folio           as FolioEntity;
use Classes\Entities\Item            as ItemEntity;
use Classes\Entities\EntityAbstract;

use Classes\Helpers\MwXml;

abstract class TaskAbstract{

	/* @var Di */
	protected $oDi;

	/* @var JobQueueDb */
	protected $oJobQueueDb;

	/* @var BoxDb */
	protected $oBoxDb;

	/* @var FolioDb */
	protected $oFolioDb;

	/* @var ItemDb */
	protected $oItemDb;

	/* @var ErrorLogDb */
	protected $oErrorLogDb;

	/* @var FileHelper */
	protected $oFile;

	/* @var Logger */
	protected $oLogger;

	protected $sProcess;

	protected function __construct( Di $oDi ){

		$this->oJobQueueDb = $oDi->get( 'Classes\Db\JobQueue' );
		$this->oBoxDb      = $oDi->get( 'Classes\Db\Box' );
		$this->oFolioDb    = $oDi->get( 'Classes\Db\Folio' );
		$this->oItemDb     = $oDi->get( 'Classes\Db\Item' );
		$this->oErrorLogDb = $oDi->get( 'Classes\Db\ErrorLog' );

		$this->oFile       = $oDi->get( 'Classes\Helpers\File' );
		$this->oLogger     = $oDi->get( 'Classes\Helpers\Logger' );

	}


	/*
	 * @return string
	 */
	protected function CreateExceptionString( \Exception $oException ){
		return $oException->getMessage() . ' ' .  $oException->getFile() . ' ' . $oException->getLine();
	}


	/*
	 *
	*/
	protected function HandleError( \Exception $oException, EntityAbstract $oEntity ){

		$this->oLogger->LogException( $oException );

		$iId = $oEntity->getId();

		$oErrorLogEntity = new ErrorLogEntity();

		switch( true ){
			case $oEntity instanceof BoxEntity:
				$oErrorLogEntity->setBoxId( $iId );
				$this->oBoxDb->UpdateProcessStatus( $iId, $this->sProcess, 'error' );
				break;
			case $oEntity instanceof FolioEntity:
				$oErrorLogEntity->setFolioId( $iId );
				$this->oFolioDb->UpdateProcessStatus( $iId, $this->sProcess, 'error' );
				break;
			case $oEntity instanceof ItemEntity:
				$oErrorLogEntity->sItemId( $iId );
				$this->oItemDb->UpdateProcessStatus( $iId, $this->sProcess, 'error' );
				break;

		}

		$sErrorString = $this->CreateExceptionString( $oException );

		$oErrorLogEntity->setJobQueueId( $this->iJobQueueId );
		$oErrorLogEntity->setProcess( $this->sProcess );
		$oErrorLogEntity->setError( $sErrorString );

		$this->oErrorLogDb->Insert( $oErrorLogEntity );


		// Escalate to the JobQueue
		throw new ImporterException( $sErrorString );

	}


	/*
	 *
	*/
	protected function PseudoSetterForAutoComplete(
													JobQueueDb  $oJobQueueDb
													, BoxDb       $oBoxDb
													, FolioDb     $oFolioDb
													, ItemDb      $oItemDb
													, ErrorLogDb  $oErrorLogDb
													, FileHelper  $oFile
													, Logger      $oLogger
													, FolioEntity $oFolioEntity
													, ItemEntity  $oItemEntity
												){

		$this->oJobQueueDb = $oJobQueueDb;
		$this->oBoxDb      = $oBoxDb;
		$this->oFolioDb    = $oFolioDb;
		$this->oItemDb 	   = $oItemDb;
		$this->oErrorLogDb = $oErrorLogDb;

		$this->oFile   = $oFile;
		$this->oLogger = $oLogger;

		$this->oFolioEntity = $oFolioEntity;
		$this->oItemEntity  = $oItemEntity;


	}

}







































