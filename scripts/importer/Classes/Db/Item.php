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

use Classes\Entities\Item as ItemEntity;

use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\Adapter\Adapter;

class Item extends DbAbstract{


	/*
	 *
	*/
	public function __construct( Adapter $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sTableName = 'cbp_items';
	}


	/*
	 *@return integer
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

		$oItemEntity->setId( $iItemId );

		return $oItemEntity;

	}


	/*
	 * @return ResultSet
	 */
	public function GetJobItemsToProcess( $iFolioId
										, $sPreviousProcess
										, $sProcess ){

		$sSql = 'SELECT
					*
				FROM
					' . $this->sTableName . '
				WHERE
					folio_id         = ?
				AND
					(( process       = ?
						AND
					  process_status = "completed" )
				OR
					process        = ? )';

		$aBindArray = array( $iFolioId
						   , $sPreviousProcess
						   , $sProcess
		 );

		$rResult   = $this->Execute( $sSql, $aBindArray );

		return $rResult;

	}

	/*
	 *
	*/
	public function ClearErrorLog( $iId ){

		$sSql = 'DELETE FROM
					cbp_error_log
				WHERE
		  			item_id = ?';

		$aBindArray = array ($iId );

		$this->Execute( $sSql, $aBindArray );

		return;

	}

}

































