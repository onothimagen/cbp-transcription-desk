<?php

namespace Classes\Db;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

abstract class DbAbstract {
	/*
	 * @var $oAdapter \Zend\Db\Adapter\Adapter
	 */
	protected $oAdapter;

	function __construct( \Zend\Db\Adapter\Adapter $oAdapter ){
		$this->oAdapter = $oAdapter;
	}


	/*
	 * @return \Zend\Db\Adapter\Driver\Pdo\Result
	 */
	protected function Execute( $sSql
							  , $aBindArray = null ){

		$stmt = $this->oAdapter->createStatement( $sSql, $aBindArray);

		try {
			$results = $stmt->execute();

		} catch (\PDOException $e) {
			throw new \Importer\Exception( 'PDO ErrorCode: ' . $e->getCode() . "\n" .
										   'PDO ErrorInfo: ' . $e->errorInfo[1] . "\n" .
										   'Parameters: ' 	 . serialize( $aBindArray )
										 );

		}

		return $results;
	}

	/*
	 * @return Zend\Db\ResultSet\ResultSet
	 */
	protected function GetResultSet( $result ){
		if ($result instanceof ResultInterface && $result->isQueryResult()) {
			$resultSet = new ResultSet;
			$resultSet->initialize( $result );

			return $resultSet;
		}

	}


}
