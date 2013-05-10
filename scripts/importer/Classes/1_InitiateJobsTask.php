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

// NOTE: This is intended to be called using PHP CLI
// You will get errors if run from PHP CGI

namespace Classes;

use Zend\Di\Di;


use Classes\Entities\JobQueue   as JobQueueEntity;
use Classes\Exceptions\Importer as ImporterException;

use Classes\Db\JobQueue as JobQueueDb;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\Pdo\Result;

class InitiateJobsTask extends TaskAbstract{

	private $rIncompleteJobs;


	public function __construct( Di $oDi ){

		parent::__construct( $oDi );

		$this->sProcess = 'initiate_jobs';

        $this->oLogger->SetContext( 'jobs' );

	}

	/*
	 * @return JobQueueEntity
	 */
	public function Execute(){

		// All jobs are complete so start a new job

        $this->rIncompleteJobs = $this->GetIncompleteJobs();

        if( $this->rIncompleteJobs === false ){
            $oJobQueueEntity = $this->CreateNewJob();
            return $oJobQueueEntity;
        }

        $oJobQueueEntity = $this->GetExistingJob();

        return $oJobQueueEntity;

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

		$sJobId = $oJobQueueEntity->getId();
		$sStep  = 'Job ' . $sJobId . ' started';
		return $oJobQueueEntity;

	}

	/*
	 * @return JobQueueEntity
	*/
	private function GetExistingJob( ){

		// If all processes have ended then start a new job
		// else restart the oldest incomplete job

		$iSpecifiedJobId = $this->GetSpecifiedId();

		if( $iSpecifiedJobId ){

			$oJobQueueEntity = $this->GetUserSpecifiedJob( $iSpecifiedJobId );

			if( $oJobQueueEntity === false ){
				return false;
			}
		}

		$oJobQueueEntity        = $this->GetOldestJob();

		$bIsProcessStillRunning = $this->IsProcessStillRunning( $oJobQueueEntity );

		if( $bIsProcessStillRunning ){
			return false;
		}

		// Flag the job has started

		$oJobQueueEntity->setStatus( 'started' );

		$this->UpdateJobQueue( $oJobQueueEntity );

		$sJobId    = $oJobQueueEntity->getId();

        $sLogData  = 'Job ' . $sJobId . ' restarted';
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
	private function GetUserSpecifiedJob( $iSpecifiedId ){

		$rUserSpecifiedJob = $this->GetSpecifiedJob( $iSpecifiedId );

		if( $rUserSpecifiedJob === false ){
            $sLogData           = $sJobId . ' no longer exists' ;
			$this->oLogger->Log( $sLogData );
			return false;
		}

		$oJobQueueEntity = $this->MapResultSetToEntity( $rUserSpecifiedJob );

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
    private function GetIncompleteJobs(){
        return $this->oJobQueueDb->GetIncompleteJobs();
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
        	$sJobId = (int) $_GET[ 'job_id' ];
            return $sJobId;
        }elseif ( isset( $argv[ 1 ] ) ){
        	$sJobId = (int) $argv[ 1 ];
            return $sJobId;
        }
        return false;
    }

}





















