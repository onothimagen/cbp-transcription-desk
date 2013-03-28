<?php

namespace Classes;

use Zend\Di\Di;

use Classes\Entities\JobQueue as JobQueueEntity;

class InitiateJobsTask extends TaskAbstract{


	public function __construct( Di $oDi ){

		parent::__construct( $oDi );

	}

	/*
	 * @return integer
	 */
	public function Execute(){

		$oJobQueueDb             = $this->oJobQueueDb;

		$bHaveAllProcessesEnded  = $oJobQueueDb->HaveAllProcessesEnded();

		$oJobQueueEntity         = new JobQueueEntity;

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

		$iJobQueueId = $oJobQueueDb->Insert( $oJobQueueEntity );

		return $iJobQueueId;

	}

}