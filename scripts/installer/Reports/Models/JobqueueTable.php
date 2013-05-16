<?php

// see http://zf2.readthedocs.org/en/latest/tutorials/tutorial.pagination.html

namespace Models;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

use Classes\Entities\JobQueue as JobQueueEntity;

class JobqueueTable extends TableAbstract{

	/*
	 *
	*/
	public function __construct( $oAdapter ){

		parent::__construct( $oAdapter );

		$this->oTableGateway = new TableGateway( 'cbp_job_queue'
											   , $oAdapter
											   , null
											   , new HydratingResultSet() );
	}

	/*
	 *
	 */
    public function FetchAll( $paginated = false ){

        if( $paginated ) {

            // create a new Select object for the table album
            $select             = new Select( 'cbp_job_queue' );

            $select->join( 'cbp_error_log'
	            		 , 'cbp_error_log.job_queue_id = cbp_job_queue.id'
	            		 , array( 'error' )
	            		 , $select::JOIN_LEFT
	            		);

            $select->order( 'job_start_time DESC' );

            // create a new result set based on the JobQueue entity
            $resultSetPrototype = new ResultSet();

            $resultSetPrototype->setArrayObjectPrototype( new JobQueueEntity() );

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







































