<?php

namespace Classes;

use Classes\Db\JobQueue as JobQueueDb;
use Classes\Entities\JobQueue as JobQueueEntity;

class InitiateJobsTask{

	private $oJobQueueDb;
	private $oJobQueueEntity;

	public function __construct(  JobQueueDb $oJobQueueDb ){

		$this->oJobQueueDb     = $oJobQueueDb;

	}

	/*
	 * @return integer
	 */
	public function Execute(){

		$oJobQueueDb             = $this->oJobQueueDb;

		$bHaveAllProcessesEnded  = $oJobQueueDb->HaveAllProcessesEnded();

		$oJobQueueEntity   = new JobQueueEntity;

		$oJobQueueEntity->setUserId( 1 );

		/*
		 * If there are jobs still queued then add to the queue and exit
		* TODO: We need a daemon to execute queued jobs
		*/

		if( $bHaveAllProcessesEnded === false){
			$oJobQueueEntity->setStatus( 'queued' );
			exit;
		}else{
			$oJobQueueEntity->setStatus( 'started' );
		}

		$iJoBQueueId = $oJobQueueDb->Insert( $oJobQueueEntity );


		return $iJoBQueueId;

	}

}