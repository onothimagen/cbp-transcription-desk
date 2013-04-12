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

use Classes\Entities\Item as ItemEntity;

class Item extends DbAbstract{


	/*
	 *
	*/
	public function __construct( $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sTableName = 'cbp_items';
	}


	/*
	 *
	 */
	public function Insert ( ItemEntity  $oItemEntity ){

		$sSql = 'INSERT INTO
					' . $this->sTableName . '
							(
							    folio_id
							  , item_number

							  , process
							  , process_status

							  , process_start_time
							  , process_end_time
							  , updated
							)
				VALUES
							(
							    ?
							  , ?

							  , \'import\'
							  , \'completed\'

							  , NOW()
							  , NOW()
							  , NOW()
							)
							ON DUPLICATE KEY UPDATE
								id = LAST_INSERT_ID( id );';

		$aBindArray = array(  $oItemEntity->getFolioId()
							, $oItemEntity->getItemNumber()
							);

		$this->Execute( $sSql, $aBindArray );

		$iItemId = $this->oAdapter->getDriver()->getLastGeneratedValue();

		return $iItemId;

	}


	/*
	 *
	 */
	public function GetJobItems(  $iFolioId
								, $sProcess
								, $sStatus ){

		$sSql = 'SELECT
					*
				FROM
					' . $this->sTableName . '
				WHERE
					folio_id    = ?
				AND
					process        = ?
				AND
					process_status = ?';

		$aBindArray = array( $iFolioId
						   , $sProcess
						   , $sStatus );

		$rResult   = $this->Execute( $sSql, $aBindArray );

		return $rResult;

	}

	/*
	 * @return boolean
	 */
	public function HasProcessesEnded(){

		$sSql = 'SELECT
					*
				FROM
					' . $this->sTableName . '
				WHERE
					process_end_time = NULL;';

		$result = $this->Execute( $sSql );

		if( $result->count() < 1 ){
			return true;
		}

		return false;

	}

}

































