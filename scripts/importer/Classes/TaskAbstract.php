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

use Classes\Db\JobQueue   as JobQueueDb;
use Classes\Db\Box        as BoxDb;
use Classes\Db\Folio      as FolioDb;
use Classes\Db\Item       as ItemDb;
use Classes\Db\ErrorLogDb as ErrorLogDb;

use Classes\Helpers\File   as FileHelper;
use Classes\Helpers\Logger;

use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Box      as BoxEntity;
use Classes\Entities\Folio    as FolioEntity;
use Classes\Entities\Item     as ItemEntity;
use Classes\Entities\ErrorLog as ErrorLogEntity;
use Classes\Entities\EntityAbstract;

use Classes\Helpers\MwXml;

use Classes\Exceptions\Importer as ImporterException;

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

	protected $sPreviousProcess;

	protected $iJobQueueId;

    protected $oJobQueueEntity;

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
	protected function HandleError( ImporterException $oException, EntityAbstract $oEntity ){

		// Create entry in error log

		$sErrorString = $this->CreateExceptionString( $oException );
		$sError       = $oException->getMessage();

		$oErrorLogEntity = new ErrorLogEntity();

		$oErrorLogEntity->setJobQueueId( $this->iJobQueueId );
		$oErrorLogEntity->setProcess( $this->sProcess );
		$oErrorLogEntity->setError( $sErrorString );


		$this->oLogger->LogException( $oException );

		$iId = $oEntity->getId();


		switch( true ){

			case $oEntity instanceof JobQueueEntity:
				$oErrorLogEntity->setJobQueueId( $iId );
                $oEntity->setStatus( 'error' );
				$this->oJobQueueDb->InsertUpdate( $oEntity );
				break;

			case $oEntity instanceof BoxEntity:

				$oErrorLogEntity->setBoxId( $iId );
				$this->oBoxDb->UpdateProcessStatus( $iId, $this->sProcess, 'error' );
				break;

			case $oEntity instanceof FolioEntity:
				$oErrorLogEntity->setFolioId( $iId );
				$this->oFolioDb->UpdateProcessStatus( $iId, $this->sProcess, 'error' );
				break;

			case $oEntity instanceof ItemEntity:
				$oErrorLogEntity->setItemId( $iId );
				$this->oItemDb->UpdateProcessStatus( $iId, $this->sProcess, 'error' );
				break;
		}

		$this->oErrorLogDb->Insert( $oErrorLogEntity );

		// Escalate to the JobQueue but we don't want to bubble higher than the job queue
		if( ( $oEntity instanceof JobQueueEntity) === true ){
			exit( $oException->getMessage() );
        }

		throw new ImporterException( $sError );


	}

	/*
	 *
	*/
	protected function ProcessBoxes(){

		$rBoxes = $this->GetBoxes( $this->sPreviousProcess, 'completed' );

		/* @var $oBoxEntity BoxEntity */
		while ( $oBoxEntity = $rBoxes->getResource()->fetchObject( 'Classes\Entities\Box' ) ){

			$iBoxId = $oBoxEntity->getId();

			$this->oBoxDb->UpdateProcessStatus( $iBoxId, $this->sProcess, 'started' );

			try {

				$this->ProcessFolios( $oBoxEntity );

			} catch ( ImporterException $oException ) {

				$this->HandleError( $oException, $oBoxEntity );
			}

			$this->oBoxDb->UpdateProcessStatus( $iBoxId, $this->sProcess, 'completed' );

		}

	}

	/*
	 *
	*/

	protected function ProcessFolios( BoxEntity $oBoxEntity ){

		$iBoxId  = $oBoxEntity->getId();

		$rFolios = $this->GetFolios( $iBoxId, $this->sPreviousProcess, 'completed' );

		/* @var $oFolioEntity FolioEntity */
		while ( $oFolioEntity = $rFolios->getResource()->fetchObject( 'Classes\Entities\Folio' ) ){

			$iFolioId = $oFolioEntity->getId();

			$this->oFolioDb->UpdateProcessStatus( $iFolioId,  $this->sProcess, 'started' );

			try {
				$this->ProcessItems( $oBoxEntity, $oFolioEntity );
			} catch ( ImporterException $oException ) {
				$this->HandleError( $oException, $oFolioEntity );
			}

			$this->oFolioDb->UpdateProcessStatus( $iFolioId, $this->sProcess, 'completed' );

		}

	}

	/*
	 *
	*/
	protected function ProcessItems( BoxEntity $oBoxEntity, FolioEntity $oFolioEntity ){

		$iFolioId = $oFolioEntity->getId();

		$rItems   = $this->GetItems( $iFolioId
								   , $this->sPreviousProcess
								   , 'completed'
								   , $this->sProcess );

		/* @var $oItemEntity ItemEntity */
		while ( $oItemEntity = $rItems->getResource()->fetchObject( 'Classes\Entities\Item' ) ){

			$iItemId = $oItemEntity->getId();

			$this->oItemDb->UpdateProcessStatus( $iItemId, $this->sProcess, 'started');

			try {

				$sItemTitle = $this->ConstructPath( $oBoxEntity, $oFolioEntity, $oItemEntity );

				// During development Comment this out to skip actual slicing
				$this->Process( $sItemTitle );

			} catch ( ImporterException $oException ) {
				$this->HandleError( $oException, $oItemEntity );
			}

			$this->oItemDb->UpdateProcessStatus( $iItemId, $this->sProcess, 'completed' );
		}

	}

	/*
	 *
	*/
	protected function GetBoxes( $sProcess, $sStatus ){

		return $this->oBoxDb->GetBoxes(
										  $this->iJobQueueId
										, $sProcess
										, $sStatus
		);

	}

	/*
	 *
	*/
	protected function GetFolios( $iBoxId, $sProcess, $sStatus ){

		return $this->oFolioDb->GetFolios(
										  $iBoxId
										, $sProcess
										, $sStatus
		);
	}

	/*
	 *
	*/
	protected function GetItems( $iFolioId
							   , $sPreviousProcess
							   , $sStatus
							   , $sProcess ){

		return $this->oItemDb->GetJobItems(
										  $iFolioId
										, $sPreviousProcess
										, $sStatus
										, $sProcess
		);
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







































