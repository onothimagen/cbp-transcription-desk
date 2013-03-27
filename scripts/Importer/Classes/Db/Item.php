<?php

namespace Classes\Db;

use Classes\Entities\Item as ItemEntity;

class Item extends DbAbstract{


	const DBNAME = 'cbp_items';

	public function Truncate(){

		$sSql = 'TRUNCATE TABLE ' . self::DBNAME . ';';

		$this->Execute( $sSql );
	}

	/*
	 *
	 */
	public function Insert ( ItemEntity  $oItemEntity ){

		$sSql = 'INSERT INTO
					' . self::DBNAME . '
							(
							    metadata_id
							  , item_number

							  , process
							  , status

							  , updated
							  , created
							)
				VALUES
							(
							    ?
							  , ?

							  , \'slice\'
							  , \'queued\'

							  , NULL
							  , NULL
							);';

		$aBindArray = array(  $oItemEntity->getMetaDataId()
							, $oItemEntity->getItemNumber()
							);

		$this->Execute( $sSql, $aBindArray );

		$iItemId = $this->oAdapter->getDriver()->getLastGeneratedValue();

		return $iItemId;

	}

	public function UpdateProcessStatus(  $iItemId
										, $sProcess
										, $sStatus ){

		$sCompleted = NULL;

		if ( $sProcess === 'verify' and $sStatus === 'completed' ){
			$sCompleted = 'NOW()';
		}

		$sSql = 'UPDATE
					' . self::DBNAME . '

				SET
					  process = ?
					, status  = ?
				    , updated = NOW()
				    , completed = ' . $sCompleted . '
				WHERE
					id = ?
				';


		$aBindArray = array (
							  $sProcess
							, $sStatus
				            , $iItemId
							);

		return  $this->Execute( $sSql, $aBindArray );

	}

	/*
	 *
	 */
	public function GetJobItems(  $iMetaDataId
								, $sProcess
								, $sStatus ){

		$sSql = 'SELECT
					*
				FROM
					' . self::DBNAME . '
				WHERE
					job_queue_id = ?
				AND
					process      = ?
				AND
					status       = ?';

		$aBindArray = array( $iMetaDataId
						   , $sProcess
						   , $sStatus );

		$rResult   = $this->Execute( $sSql, $aBindArray );

		return $rResult;

	}

	/*
	 * @return boolean
	 */
	public function HaveAllProcessesEnded(){

		$sSql = 'SELECT
					*
				FROM
					' . self::DBNAME . '
				WHERE
					completed	= NULL;';

		$result = $this->Execute( $sSql );

		if( $result->count() < 1 ){
			return true;
		}

		return false;

	}

}

































