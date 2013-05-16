<?php

// see http://zf2.readthedocs.org/en/latest/tutorials/tutorial.pagination.html

namespace Models;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

use Classes\Entities\Box      as BoxEntity;


class BoxTable extends TableAbstract{

	/*
	 *
	*/
	public function __construct( $oAdapter ){

		parent::__construct( $oAdapter );

		$this->oTableGateway = new TableGateway( 'cbp_boxes'
											   , $oAdapter
											   , null
											   , new HydratingResultSet() );
	}

	/*
	 *
	 */
    public function FetchAll( $paginated = false, $iId ){

        if( $paginated ) {

            // create a new Select object for the table album
            $select = new Select( 'cbp_boxes' );

            $select->join( 'cbp_error_log'
	            		 , 'cbp_error_log.box_id = cbp_boxes.id'
	            		 , array( 'error' )
	            		 , $select::JOIN_LEFT
	            		);

            $select->where( array('cbp_boxes.job_queue_id' => $iId ));

            $select->order( 'process_start_time DESC' );

            // create a new result set based on the Box entity
            $resultSetPrototype = new ResultSet();

            $resultSetPrototype->setArrayObjectPrototype( new BoxEntity() );

            // create a new pagination adapter object
            $paginatorAdapter = new DbSelect(
							                // our configured select object
							                  $select
							                // the adapter to run it against
							                , $this->oTableGateway->getAdapter()
							                // the result set to hydrate
							                , $resultSetPrototype
							            );

            $paginator        = new Paginator( $paginatorAdapter );

            return $paginator;
        }
        $resultSet = $this->oTableGateway->select();

        return $resultSet;
    }

}