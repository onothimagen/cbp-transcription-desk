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

// NOTE: This is intended to be called using PHP CLI
// You will get errors if run from PHP CGI

namespace Classes;

use Zend\Di\Di;

use Classes\Entities\JobQueue   as JobQueueEntity;
use Classes\Exceptions\Importer as ImporterException;

use Classes\Db\JobQueue as JobQueueDb;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\Pdo\Result;

/*
 * This class is called by 1_InitiateJob.php
 * It creates a new Job by retrieving an existing one from the DB or creating a new one
 * If another existing job is running it will exit
 * If the same job is currently running under another process than that process will be killed
 */
class InitiateJobsTask extends TaskAbstract{

	private $rIncompleteJobs;

	private $sImageImportPath;

	private $sBoxPrefix;

	/*
	 * @param Di $oDi
	 * @param string[] $aConfig
	 * @return void
	 */
	public function __construct( Di $oDi, $aConfig ){

		parent::__construct( $oDi );

		$this->sProcess         = 'initiate_jobs';

        if( !array_key_exists( 'path.image.import', $aConfig ) or empty( $aConfig[ 'path.image.import' ]) ){
      			throw new ImporterException( 'path.image.import is missing or not configured in config.ini.php ' );
        }

		$this->sImageImportPath = $aConfig[ 'path.image.import' ];

        if( !array_key_exists( 'box.prefix', $aConfig ) or empty( $aConfig[ 'box.prefix' ]) ){
      			throw new ImporterException( 'box.prefix is missing or not configured in config.ini.php ' );
        }

		$this->sBoxPrefix       = $aConfig[ 'box.prefix' ];

	}



	/*
	 * Entry point to start task
	 *
	 * @return JobQueueEntity|boolean
	 */
	public function Execute(){

		try {

			$this->oLogger->ConfigureLogger( 'jobs' );

			return $this->CreateJob();


		} catch ( ImporterException $oException ) {

			exit( $oException->getMessage() );

		}

	}




	/*
	 * Checks to see if there are any new boxes and exit if none are found
	*
	* @return void
	*/
	private function BackOutofJobIfNoImages(){

		$sImageImportPath = $this->sImageImportPath;
		$sBoxPrefix       = $this->sBoxPrefix;

		$aBoxes           = $this->oFile->ScanImageDirectory( $sImageImportPath, $sBoxPrefix );

		if( count ( $aBoxes ) > 0 ){
			return;
		}

		$sLogData = 'No boxes found. Exiting ....';

		$this->oLogger->Log( $sLogData );

		// Nothing more to do
		exit ();

	}


	/*
	 * @return JobQueueEntity|boolean
	 */
	private function CreateJob(){

		/* Has a specific job been specified? */

		$iSpecifiedJobId = $this->GetSpecifiedId();

		if( $iSpecifiedJobId !== false ){

			/* Are there any other jobs running other than the job being restarted? If there are then stop*/

			$this->rIncompleteJobs = $this->GetIncompleteJobs( $iSpecifiedJobId );

			if( $this->rIncompleteJobs ){
				$sLogData  = 'There are existing incomplete jobs. Exiting...';
				$this->oLogger->Log( $sLogData );
				return false;
			}

			$oJobQueueEntity = $this->GetUserSpecifiedJob( $iSpecifiedJobId );

			if( $oJobQueueEntity === false ){
				$sLogData  = 'Job ' . $iSpecifiedJobId . ' could not be found. Exiting...';
				$this->oLogger->Log( $sLogData );
				return false;
			}

			$this->oJobQueueDb->ClearErrorLog( $iSpecifiedJobId );

			return $oJobQueueEntity;
		}

		/* Check if there are any images to process */

		$this->BackOutofJobIfNoImages();

		$this->rIncompleteJobs = $this->GetIncompleteJobs();

		/* If all jobs are complete so start a new job */

		if( $this->rIncompleteJobs === false ){
			$oJobQueueEntity = $this->CreateNewJob();
			return $oJobQueueEntity;
		}

		$sLogData  = 'There are existing incomplete jobs. Exiting...';
		$this->oLogger->Log( $sLogData );

		return false;
	}


	/*
	 *
	 */
	private function KillJobProcess( JobQueueEntity $oJobQueueEntity ){

		$iPid = $oJobQueueEntity->getPid();

		if( (int) $iPid === 0 ){
			return;
		}

		$bProcessExists = false;

		$bProcessExists = $this->oFile->ProcessExists( $iPid );

		if( $bProcessExists === false ){
			$sLogData = 'Process ' . $iPid . ' has already terminated' ;
			$this->oLogger->Log( $sLogData );
			return;
		}

		$sLogData = $iPid . ' is still running';
		$this->oLogger->Log( $sLogData );

		$this->oFile->KillProcess( $iPid );

		$sLogData = 'Process ID ' . $iPid . ' has been killed';
		$this->oLogger->Log( $sLogData );
	}


	/*
	 * @return JobQueueEntity
	*/
	private function CreateNewJob(){

		$oJobQueueEntity = new JobQueueEntity;
		$oJobQueueEntity->setUserId( 1 );

		$iProcessId = getmypid();

		$oJobQueueEntity->setPid( $iProcessId );
		$oJobQueueEntity = $this->oJobQueueDb->InsertUpdate( $oJobQueueEntity );

		$sJobId    = $oJobQueueEntity->getId();
		$sLogData  = 'Job ' . $sJobId . ' started';
		$this->oLogger->Log( $sLogData );

		return $oJobQueueEntity;

	}


	/*
	 * @return boolean
	 */
	private function IsProcessStillRunning( JobQueueEntity $oJobQueueEntity){

		$sJobId  = $oJobQueueEntity->getId();
		$iOldPid = $oJobQueueEntity->getPid();

		if( $iOldPid === null ){
			return false;
		}

		$bIsProcessStillRunning = $this->oFile->PidExists( $iOldPid );

		// Exit if it is still running

		if( $bIsProcessStillRunning ){
            $sLogData = $sJobId . ' . is still running with pid ' . $iOldPid ;
			$this->oLogger->Log( $sLogData );
			return true;
		}

		return false;
	}

	/*
	 * @return JobQueueEntity
	*/
	private function GetUserSpecifiedJob( $Id ){

		$rUserSpecifiedJob = $this->GetSpecifiedJob( $Id );

		if( $rUserSpecifiedJob === false ){
            $sLogData           = $Id . ' no longer exists' ;
			$this->oLogger->Log( $sLogData );
			return false;
		}

		$oJobQueueEntity = $this->MapResultSetToEntity( $rUserSpecifiedJob );

		/* Kill any existing pid */
		$this->KillJobProcess( $oJobQueueEntity );

		$sAction = $this->GetAction();

		if( $sAction == 'stop' ){
			$sLogData  = 'Job ' . $Id . ' has been stopped';
			$this->oLogger->Log( $sLogData );
			exit();
		}

		$iProcessId = getmypid();

		$oJobQueueEntity->setPid( $iProcessId );
		$oJobQueueEntity = $this->oJobQueueDb->InsertUpdate( $oJobQueueEntity );

		$sJobId    = $oJobQueueEntity->getId();
		$sLogData  = 'Job ' . $sJobId . ' re-started';
		$this->oLogger->Log( $sLogData );

		return $oJobQueueEntity;

	}


	/*
	 * @return JobQueueEntity
	*/
	private function GetOldestJob(){

		$rJobs = $this->rIncompleteJobs;

		$oJobQueueEntity = $this->MapResultSetToEntity( $rJobs );

		if( $oJobQueueEntity === false ){
			throw new ImporterException( 'RestartOldestJob() should have returned a result if $bHaveAllProcessesEnded is false' );
		}

		return $oJobQueueEntity;
	}


	/*
	 * @return JobQueueEntity
	*/
    private function MapResultSetToEntity( Result $rJobs ){

      	/* @var $oJobQueueEntity JobQueueEntity */
      	$oJobQueueEntity = $rJobs->getResource()->fetchObject( 'Classes\Entities\JobQueue' );

        if( $oJobQueueEntity instanceof JobQueueEntity === false ){
            throw new ImporterException( 'Mapping result set to instance of JobQueue Entity failed' );
        }

        return $oJobQueueEntity;
    }


    /*
     * @return ResultSet | boolean
     */
    private function GetIncompleteJobs( $iSpecifiedJobId = null ){
        return $this->oJobQueueDb->GetIncompleteJobs( $iSpecifiedJobId );
    }

    /*
     * @return ResultSet
     */
    private function GetSpecifiedJob( $iJobId ){
        return $this->oJobQueueDb->GetJob( $iJobId );
    }

    /*
     *
     */
    private function UpdateJobQueue( JobQueueEntity $oJobQueueEntity ){
        $this->oJobQueueDb->UpdateJobQueue( $oJobQueueEntity );
    }

	/*
	 * @return integer | boolean
	 */
    private function GetSpecifiedId(){
    	global $argv;

        if( isset( $_GET[ 'job_id' ] ) ){
            return  (int) $_GET[ 'job_id' ];
        }elseif ( isset( $argv[ 1 ] ) ){
        	return (int) $argv[ 1 ];
        }
        return false;
    }

    function GetAction(){
    	global $argv;

    	if( isset( $_GET[ 'action' ] ) ){
    		return $_GET[ 'action' ];
    	}elseif ( isset( $argv[ 2 ] ) ){
    		return $argv[ 2 ];
    	}
    	return '';
    }

}
































































