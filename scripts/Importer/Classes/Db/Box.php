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

class Box extends DbAbstract{


	public function __construct( $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sDbname = 'cbp_boxes';
	}

	/*
	 * @return BoxEntity
	 */
	public function Insert ( BoxEntity $oBoxEntity ){

		$sSql = 'INSERT INTO
					' . $this->sDbname . '
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
	public function GetBoxes(
							  $iJobQueueId
							, $sProcess
							, $sStatus ){

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
					' . $this->sDbname . '
				WHERE
					job_queue_id   = ?
				AND
					process        = ?
				AND
					process_status = ?
				ORDER BY
					box_number ASC';


		$aBindArray = array(
							  $iJobQueueId
							, $sProcess
							, $sStatus
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
								, $sProcess
								, $sStatus ){

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
					' . $this->sDbname . '
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
					cbp_items.process        = ?
				AND
					cbp_items.process_status = ?
				ORDER BY
					box_number ASC
				  , folio_number ASC
				  , item_number ASC;';


		$aBindArray = array(
							 $iJobQueueId
							, $sProcess
							, $sStatus
		);

		$rResult   = $this->Execute( $sSql, $aBindArray );

		return $rResult;
	}


}

































