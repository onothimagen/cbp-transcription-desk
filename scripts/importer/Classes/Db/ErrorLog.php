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

use Classes\Entities\ErrorLog as ErrorLogEntity;

class ErrorLog extends DbAbstract{


	/*
	 *
	*/
	public function __construct( $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sTableName = 'cbp_error_log';
	}

	/*
	 *
	 */
	public function Insert ( ErrorLogEntity $oErrorLogEntity ){

		$sSql = 'INSERT INTO
					' . $this->sTableName . '
									(
									   job_queue_id
									 , box_id
									 , folio_id
									 , item_id
									 , process
									 , error
									 , created
									)
				VALUES
									(
									   ?
									 , ?
									 , ?
									 , ?
									 , ?
									 , ?
									 , ?
									)
				ON DUPLICATE KEY UPDATE
									error           = ?';

		$aBindArray = array(
							   $oErrorLogEntity->getJobQueueId()
							 , $oErrorLogEntity->getBoxId()
							 , $oErrorLogEntity->getFolioId()
							 , $oErrorLogEntity->getItemId()
							 , $oErrorLogEntity->getProcess()
							 , $oErrorLogEntity->getError()
							 , $oErrorLogEntity->getCreated()
							 , $oErrorLogEntity->getError()
							);

		$this->Execute( $sSql, $aBindArray );

		return;

	}


}
































































