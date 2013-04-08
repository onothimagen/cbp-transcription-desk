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

namespace Classes\Db;

use Classes\Entities\JobQueue as JobQueueEntity;

class JobQueue extends DbAbstract{


	/*
	 *
	*/
	public function __construct( $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sDbname = 'cbp_job_queue';
	}


	/*
	 *
	 */
	public function Insert ( JobQueueEntity $oJobQueueEntity ){

		$sStatus = $oJobQueueEntity->getStatus();

		$sStartTime = 'NULL';

		if( $sStatus == 'started' ){
			$sStartTime = 'NOW()';
		}

		$sEndTime = 'NULL';

		if( $sStatus == 'completed' ){
			$sEndTime = 'NOW()';
		}


		if( $oJobQueueEntity->getEnded() == NULL ){
			$oJobQueueEntity->setEnded( 'NULL' );
		}

		$sSql = 'INSERT INTO
					' . $this->sDbname . '
								(   user_id
								  , status
								  , job_start_time
								  , job_end_time
								)
				VALUES
								(
								    ?
								  , ?
								  , ' .  $sStartTime .'
								  , ' .  $sEndTime   .'
								)
					ON DUPLICATE KEY UPDATE
						  id = id;';

		$aBindArray = array(
							  $oJobQueueEntity->getUserId()
							, $oJobQueueEntity->getStatus()
							);

		$this->Execute( $sSql, $aBindArray );

		$oJobQueueId = $this->oAdapter->getDriver()->getLastGeneratedValue();

		return $oJobQueueId;

	}


	/*
	 *
	*/
	public function UpdateJobStatus(
											$iJobQueueId
										  , $sStatus ){

		$sJobEndTime = 'NULL';

		if ( $sStatus === 'completed' ){
			$sJobEndTime = 'NOW()';
		}

		$sSql = 'UPDATE
					' . $this->sDbname . '

				SET
					  status       = ?
				    , job_end_time = ' . $sJobEndTime . '
				WHERE
				    id             = ?';


		$aBindArray = array (
							  $sStatus
							, $iJobQueueId
							);

		return  $this->Execute( $sSql, $aBindArray );

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
					job_end_time = NULL;';

		$result = $this->Execute( $sql );

		if( $result->count() < 1 ){
			return true;
		}

		return false;

	}


}






























