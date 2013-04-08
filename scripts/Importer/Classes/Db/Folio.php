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

use \Classes\Entities\Folio as FolioEntity;
use \Zend\Db\ResultSet\ResultSet;

class Folio extends DbAbstract{


	/*
	 *
	 */
	public function __construct( $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sDbname = 'cbp_folios';
	}

	/*
	 * @return
	 */
	public function Insert( FolioEntity $oFolioEntity ){

		$sSql = 'INSERT INTO
					' . $this->sDbname . '
								(
									  id_number
									, box_id
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
									, process
									, process_status
									, process_start_time
									, updated
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
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, ?
									, \'import\'
									, \'started\'
									, NOW()
									, NOW()
								)
							ON DUPLICATE KEY UPDATE
								updated = VALUES(updated);';

		$aBindArray = array (  (int) $oFolioEntity->getIdNumber()
								 , $oFolioEntity->getBoxId()
								 , $oFolioEntity->getFolioNumber()
								 , $oFolioEntity->getSecondFolioNumber()
								 , $oFolioEntity->getCategory()
								 , $oFolioEntity->getRectoVerso()
								 , $oFolioEntity->getCreator()
								 , $oFolioEntity->getRecipient()
								 , $oFolioEntity->getPenner()
								 , $oFolioEntity->getMarginals()
								 , $oFolioEntity->getCorrections()
								 , $oFolioEntity->getDate_1()
								 , $oFolioEntity->getDate_2()
								 , $oFolioEntity->getDate_3()
								 , $oFolioEntity->getDate_4()
								 , $oFolioEntity->getDate_5()
								 , $oFolioEntity->getDate_6()
								 , $oFolioEntity->getEstimatedDate()
								 , $oFolioEntity->getInfoInMainHeadingField()
								 , $oFolioEntity->getMainHeading()
								 , $oFolioEntity->getSubHeadings()
								 , $oFolioEntity->getMarginalSummaryNumbering()
								 , $oFolioEntity->getNumberOfPages()
								 , $oFolioEntity->getPageNumbering()
								 , $oFolioEntity->getTitles()
								 , $oFolioEntity->getWatermarks()
								 , $oFolioEntity->getPaperProducer()
								 , $oFolioEntity->getPaperProducerInYear()
								 , $oFolioEntity->getNotesPublic()
								);

		$Result = $this->Execute( $sSql, $aBindArray );


		$iInsertId = $this->oAdapter->getDriver()->getLastGeneratedValue();

		// Duplicate found

		if( $iInsertId === '0' ){
			return false;
		}

		$oFolioEntity->setId( (int) $iInsertId );

		return $oFolioEntity;

	}



	/*
	 * @param string $sJobQueueId
	 * @return ResultSet
	 */
	public function GetFolios(
								$iBoxId
							  , $sProcess
							  , $sStatus ){

		$sSql = 'SELECT
					*
				FROM
					' . $this->sDbname . '
				WHERE
					box_id  = ?
				AND
					process = ?
				AND
					process_status  = ?;';


		$aBindArray = array(
							  $iBoxId
							, $sProcess
							, $sStatus
							);

		$rResult   = $this->Execute( $sSql, $aBindArray );


		return $rResult;
	}



	
	
	
}

































