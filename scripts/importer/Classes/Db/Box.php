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

use Classes\Entities\Box as BoxEntity;

use Classes\Exceptions\Importer as ImporterException;

class Box extends DbAbstract{


	public function __construct( $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sTableName = 'cbp_boxes';
	}

	/*
	 * @return BoxEntity
	 */
	public function Insert ( BoxEntity $oBoxEntity ){

		$sSql = 'INSERT INTO
					' . $this->sTableName . '
							(
							    job_queue_id
							  , box_number
							  , process
							  , process_status
							  , process_start_time
							  , updated
							)
				VALUES
							(
							    ?
							  , ?
							  , "import"
							  , "started"
							  , NOW()
							  , NOW()
							)
				 ON DUPLICATE KEY UPDATE
							id = LAST_INSERT_ID( id );';

		$aBindArray = array(
							  $oBoxEntity->getJobQueueId()
						    , $oBoxEntity->getBoxNumber()
							);

		$this->Execute( $sSql, $aBindArray );

		$iBoxId = $this->oAdapter->getDriver()->getLastGeneratedValue();

		$oBoxEntity->setId( $iBoxId );

		return $oBoxEntity;

	}


	/*
	 * @param integer $iJobQueueId
	* @param string $sProcess
	* @param string $sStatus
	* @return ResultSet
	*/
	public function GetBoxes( $iJobQueueId
							, $sPreviousProcess
							, $sProcess ){

		$sSql = 'SELECT
					   id
					 , job_queue_id
					 , box_number
					 , process
					 , process_status
					 , process_start_time
					 , process_end_time
					 , updated
					 , created

				FROM
					' . $this->sTableName . '
				WHERE
					job_queue_id   = ?
				AND
					( ( process       = ?
						AND
					  process_status = "completed" )
				OR
					process        = ? )
				ORDER BY
					box_number ASC';


		$aBindArray = array(
							  $iJobQueueId
							, $sPreviousProcess
							, $sProcess
					);

		$rResult   = $this->Execute( $sSql, $aBindArray );

		return $rResult;
	}



	/*
	 * @param integer $iJobQueueId
	* @param string $sProcess
	* @param string $sStatus
	* @return ResultSet
	*/
	public function GetJobItems( $iJobQueueId
							   , $sPreviousProcess
							   , $sStatus
							   , $sProcess ){

		$sSql = 'SELECT
					  cbp_items.id
					, box_number
					, folio_number
					, second_folio_number
					, category
					, recto_verso
					, creator
					, recipient
					, penner
					, marginals
					, corrections
					, date_1
					, date_2
					, date_3
					, date_4
					, date_5
					, date_6
					, estimated_date
					, info_in_main_heading_field
					, main_heading
					, sub_headings
					, marginal_summary_numbering
					, number_of_pages
					, page_numbering
					, titles
					, watermarks
					, paper_producer
					, paper_producer_in_year
					, notes_public
					, job_queue_id
					, cbp_items.id as item_id
					, item_number

				FROM
					' . $this->sTableName . '
				LEFT JOIN
				    cbp_folios
				ON
					cbp_boxes.id             = cbp_folios.box_id
				LEFT JOIN
					cbp_items
				ON
				    cbp_folios.id            = cbp_items.folio_id
				WHERE
					cbp_boxes.job_queue_id   = ?
				AND
					(( cbp_items.process       = ?
						AND
					  cbp_items.process_status = ? )
				OR
					( cbp_items.process        = ?
						AND
					  cbp_items.process_status = "error" ))
				ORDER BY
					box_number ASC
				  , folio_number ASC
				  , item_number ASC;';


		$aBindArray = array(
							  $iJobQueueId
							, $sPreviousProcess
							, $sStatus
							, $sProcess
		);

		$rResult   = $this->Execute( $sSql, $aBindArray );

		return $rResult;
	}


	/*
	 *
	*/
	public function FlagJobProcessAsStarted(
											  $iJobQueueId
											, $sProcess
									       ){

		$aProcessList = array( 'slice'
							 , 'export'
							 , 'import_mw'
							 , 'verify'
							 , 'archive'
							);

		if( in_array( $sProcess, $aProcessList ) === false ) {
			throw new ImporterException( 'Process ' . $sProcess . ' passed to FlagJobProcessAsStarted() is not a valid process' );
		}

		$sSql = 'UPDATE
					cbp_boxes
				 JOIN
					cbp_folios
				 ON
					cbp_boxes.id = cbp_folios.box_id
				 JOIN
					cbp_items
				 ON
					cbp_folios.id = cbp_items.folio_id
				 SET
 					  cbp_boxes.process            = ?
					, cbp_boxes.process_status     = "started"
					, cbp_boxes.process_start_time = NOW()
					, cbp_boxes.process_end_time   = NULL
				    , cbp_boxes.updated            = NOW()

					, cbp_folios.process           = ?
					, cbp_folios.process_status    = "started"
					, cbp_folios.process_start_time = NOW()
					, cbp_folios.process_end_time  = NULL
				    , cbp_folios.updated           = NOW()

					, cbp_items.process            = ?
					, cbp_items.process_status     = "started"
					, cbp_items.process_start_time = NOW()
					, cbp_items.process_end_time   = NULL
				    , cbp_items.updated            = NOW()
				 WHERE
					cbp_boxes.job_queue_id         = ?';


		$aBindArray = array (
							  $sProcess
							, $sProcess
							, $sProcess
							, $iJobQueueId
							);


		$this->Execute( $sSql, $aBindArray );

		return;

	}



	/*
	 *
	*/
	public function FlagJobProcessAsCompleted( $iJobQueueId , $sProcess ){

		$aProcessList = array( 'slice', 'export', 'import_mw', 'verify', 'archive' );

		if( in_array( $sProcess, $aProcessList ) === false ) {
			throw new ImporterException( 'Process ' . $sProcess . ' passed to FlagJobProcessAsCompleted() is not a valid process' );
		}

		$sJobEndTime = 'NULL';

		if ( $sProcess === 'verify'){
			$sJobEndTime = 'NOW()';
		}


		$sSql = 'UPDATE
					cbp_boxes
				 JOIN
					cbp_folios
				 ON
					cbp_boxes.id = cbp_folios.box_id
				 JOIN
					cbp_items
				 ON
					cbp_folios.id = cbp_items.folio_id
				 SET
					  cbp_boxes.process_status    = "completed"
					, cbp_boxes.process_end_time  = NOW()
				    , cbp_boxes.updated           = NOW()

					, cbp_folios.process_status   = "completed"
					, cbp_folios.process_end_time = NOW()
				    , cbp_folios.updated          = NOW()

					, cbp_items.process_status    = "completed"
					, cbp_items.process_end_time  = NOW()
				    , cbp_items.updated           = NOW()

				 WHERE
					cbp_boxes.job_queue_id         = ?
				 AND
					cbp_boxes.process              = ?
				 AND
					cbp_boxes.process_status       = "started"
				 AND
					cbp_folios.process             = ?
				 AND
					cbp_folios.process_status      = "started"
				 AND
					cbp_items.process              = ?
				 AND
					cbp_items.process_status       = "started"';


		$aBindArray = array (
							  $iJobQueueId
							, $sProcess
							, $sProcess
							, $sProcess );


		$this->Execute( $sSql, $aBindArray );

		return;

	}


	/*
	 *
	*/
	public function ClearErrorLog( $iId ){

		$sSql = 'DELETE FROM
					cbp_error_log
				WHERE
		  			box_id = ?';

		$aBindArray = array ($iId );

		$this->Execute( $sSql, $aBindArray );

		return;

	}


}

































