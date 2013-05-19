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

namespace Classes\Db;

use Classes\Entities\JobQueue   as JobQueueEntity;
use Classes\Exceptions\Importer as ImporterException;

use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\Adapter\Adapter;

class JobQueue extends DbAbstract{


	/*
	 *
	*/
	public function __construct( Adapter $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sTableName = 'cbp_job_queue';
	}


	/*
	 * @param JobQueueEntity
	 * @return JobQueueEntity
	 */
	public function InsertUpdate ( JobQueueEntity $oJobQueueEntity ){

        $sEndTime = ', job_end_time = NULL';

        $sStatus = $oJobQueueEntity->getStatus();

        if( $sStatus === NULL or $sStatus === 'started' ){
            $sStatus    = 'started';
        }

        if( $sStatus == 'completed' ){
			$sEndTime = ', job_end_time = NOW()';
		}

		$sSql = 'INSERT INTO
					' . $this->sTableName . '
							(   id
							  , user_id
							  , job_status
							  , job_start_time
							  , job_end_time
							  , pid
							)
				VALUES
							(   ?
							  , ?
							  , "started"
							  , NOW()
							  , NULL
							  , ?
							)
				ON DUPLICATE KEY UPDATE
					    id           = id
					  , job_status   = ?
					  ' . $sEndTime . '
					  , pid			 = ?';

		$aBindArray = array(
                              $oJobQueueEntity->getId()
                            , $oJobQueueEntity->getUserId()
							, $oJobQueueEntity->getPid()
							, $sStatus
							, $oJobQueueEntity->getPid()
		 					);

		$this->Execute( $sSql, $aBindArray );

		$oJobQueueId = $this->oAdapter->getDriver()->getLastGeneratedValue();

		$oJobQueueEntity->setId( $oJobQueueId );

		return $oJobQueueEntity;

	}



	/*
	 * @param JobQueueEntity
	 * @return JobQueueEntity
	 */
	public function UpdateJobQueue( JobQueueEntity $oJobQueueEntity ){

        $iId     = $oJobQueueEntity->getId();
        $iUserId = $oJobQueueEntity->getUserId();
        $sStatus = $oJobQueueEntity->getStatus();

        $sJobStartTime = '';
		$sJobEndTime   = 'NULL';

        if( $sStatus === 'started' ){
            $sJobStartTime = 'job_start_time = NOW(),';
        }

		if ( $sStatus === 'completed' ){
			$sJobEndTime = 'NOW()';
		}

		$sSql = 'UPDATE
					' . $this->sTableName . '

				SET
				  ' . $sJobStartTime . '
					  job_status       = ?
				    , job_end_time     = ' . $sJobEndTime . '
				WHERE
				    id                 = ?';


		$aBindArray = array (
							  $sStatus
							, $iId
							);

		$this->Execute( $sSql, $aBindArray );

	}


	/*
	 *
	 * @return Result
	 */
	public function GetIncompleteJobs( $iSpecifiedJobId = null){

		$sWhereClause = '';

		if( $iSpecifiedJobId !== null){
			$sWhereClause = 'AND id <> ' . $iSpecifiedJobId;
		}

		$sql = 'SELECT
					*
				FROM
					' . $this->sTableName . '
				WHERE
					job_end_time IS NULL
				' . $sWhereClause . '
				ORDER by
				    job_start_time DESC;';

		$rResult = $this->Execute( $sql );

		if( $rResult->count() > 0 ){
			return true;
		}

		return false;

	}


	/*
	 *
	 * @return Result | boolean
	 */
	public function GetJob( $iJobId ){

		$sSql = 'SELECT
					*
				FROM
					' . $this->sTableName . '
				WHERE
				    id = ?;';

        $aBindArray = array ( $iJobId );

        $rResult = $this->Execute( $sSql, $aBindArray );

        if( $rResult->count() > 0 ){
      			return $rResult;
      	}

        return false;

	}


	/*
	*
	* @return Result | boolean
	*/
	public function GetAllJobs(){

		$sSql = 'SELECT
					*
				FROM
					' . $this->sTableName . ';';

		$rResult = $this->Execute( $sSql);

		if( $rResult->count() > 0 ){
			return $rResult;
		}

		return false;

	}

	/*
	 *
	*/
	public function ClearErrorLog( $iId ){

		$sSql = 'DELETE FROM
					cbp_error_log
				WHERE
		  			job_queue_id = ?';

		$aBindArray = array ( $iId );

		$this->Execute( $sSql, $aBindArray );

		return;

	}

	/*
	 * @return  Result | boolean
	 */
	public function IsJobEmpty( $iId ){

		$sSql = 'SELECT *

				FROM
					' . $this->sTableName . '
				LEFT JOIN
				    cbp_boxes
				ON
					cbp_job_queue.id   = cbp_boxes.job_queue.id
				WHERE
					cbp_job_queue.id   = ?';

		$aBindArray = array ( $iId );

		$this->Execute( $sSql, $aBindArray );

		if( $rResult->count() > 0 ){
			return $rResult;
		}

		return false;

	}

	/*
	 *@return void
	 */
	public function RemoveJob( $iId ){

		$sSql = 'DELETE FROM
					' . $this->sTableName . '
				WHERE
		  			id = ?';

		$aBindArray = array ( $iId );

		$this->Execute( $sSql, $aBindArray );

		return;


	}


}






























