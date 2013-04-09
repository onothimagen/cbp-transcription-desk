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


use Classes\Entities\JobQueue as JobQueueEntity;

use Classes\Db\JobQueue as JobQueueDb;

class InitiateJobsTask extends TaskAbstract{


	public function __construct( Di $oDi ){

		parent::__construct( $oDi );

	}

	/*
	 * @return JobQueueEntity
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

		$oJobQueueEntity = $oJobQueueDb->Insert( $oJobQueueEntity );

		return $oJobQueueEntity;

	}

}