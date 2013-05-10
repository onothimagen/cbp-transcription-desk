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

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

use Classes\Exceptions\Importer as ImporterException;

abstract class DbAbstract {

	protected $sTableName;

	/*
	 * @var $oAdapter Adapter
	 */
	protected $oAdapter;

	function __construct( \Zend\Db\Adapter\Adapter $oAdapter ){
		$this->oAdapter = $oAdapter;
	}


	/*
	 * @return Result
	 */
	protected function Execute( $sSql
							  , $aBindArray = null ){

		$stmt = $this->oAdapter->createStatement( $sSql, $aBindArray);

		try {
			$results = $stmt->execute();

		} catch (\PDOException $e) {
			throw new ImporterException( 'PDO ErrorCode: ' . $e->getCode() . "\n" .
										 'PDO ErrorInfo: ' . $e->errorInfo[1] . "\n" .
										 'Parameters: '    . serialize( $aBindArray )
										 );

		}

		return $results;
	}

	public function Truncate(){

		$sSql = 'TRUNCATE TABLE ' . $this->sTableName . ';';
		$this->Execute( $sSql );
	}

	public function UpdateProcessStatus(
										  $iId
										, $sProcess
										, $sStatus
										){
		$sTime = '';

		if( $sStatus === 'started' ){
			$sTime = ', process_start_time = NOW(), process_end_time   = NULL';
		}

		if( $sStatus === 'error' ){
			$sTime = ', process_end_time = NULL';
		}

		if( $sStatus === 'completed'	){
			$sTime = ', process_end_time   = NOW()';
		}

		$sSql = 'UPDATE
					' . $this->sTableName . '
				SET
					    process        = ?
					  , process_status = ?
					  ' . $sTime .'
					  , updated        = NOW()
		  		WHERE
		  			id = ?';

		  		$aBindArray = array (
					  				  $sProcess
					  				, $sStatus
					  				, $iId
							  		);

  		$this->Execute( $sSql, $aBindArray );

  		return;

	}



}
