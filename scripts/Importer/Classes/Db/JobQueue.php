<?php

namespace Classes\Db;

class JobQueue extends DbAbstract{

	/*
	 *
	 */
	public function Insert ( \Classes\Entities\JobQueue $oJobQueueEntity ){

		$sStatus = $oJobQueueEntity->getStatus();

		if( $sStatus == 'started' ){
			$oJobQueueEntity->setStarted( 'NOW()' );
		}

		if( $sStatus == 'ended' ){
			$oJobQueueEntity->setEnded( 'NOW()' );
		}

		if( $oJobQueueEntity->getEnded() == NULL ){
			$oJobQueueEntity->setEnded( 'NULL' );
		}

		$sSql = 'INSERT INTO
					cbp_job_queue
								(  id
								  ,user_id
								  ,status
								  ,started
								  ,ended
								  ,created
								)
				VALUES
								(
								   ?
								  ,?
								  ,?
								  ,' .  $oJobQueueEntity->getStarted() .'
								  ,' .  $oJobQueueEntity->getEnded()   .'
								  ,?
								)
					ON DUPLICATE KEY UPDATE
						  status  = VALUES( status )
						, started = VALUES( started )
						, ended	  = VALUES( ended );';

		$aBindArray = array(
							  $oJobQueueEntity->getId()
							, $oJobQueueEntity->getUserId()
							, $oJobQueueEntity->getStatus()
							, $oJobQueueEntity->getCreated()
							);

		$this->Execute( $sSql, $aBindArray );

		$oJobQueueId = $this->oAdapter->getDriver()->getLastGeneratedValue();

		return $oJobQueueId;

	}

	/*
	 *
	 */
	public function HaveAllProcessesEnded(){

		$sql = 'SELECT
					*
				FROM
					cbp_job_queue
				WHERE
					ended	= NULL;';

		$result = $this->Execute( $sql );

		if( $result->count() < 1 ){
			return true;
		}

		return false;

	}


}






























