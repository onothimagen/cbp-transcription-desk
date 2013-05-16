<?php

// see http://zf2.readthedocs.org/en/latest/tutorials/tutorial.pagination.html

namespace Models;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

use Classes\Entities\Item as ItemEntity;


class ItemTable extends TableAbstract{

	/*
	 *
	*/
	public function __construct( $oAdapter ){

		parent::__construct( $oAdapter );

		$this->oTableGateway = new TableGateway( 'cbp_items'
											   , $oAdapter
											   , null
											   , new HydratingResultSet() );
	}

	/*
	 *
	 */
    public function FetchAll( $paginated = false, $iFolioId ){

        if( $paginated ) {

            // create a new Select object for the table album
            $select = new Select( 'cbp_items' );

            $select->join( 'cbp_folios'
            			 , 'cbp_folios.id = cbp_items.folio_id'
            		 	 , array( 'folio_number' )
      					 );

            $select->join( 'cbp_error_log'
	            		 , 'cbp_error_log.item_id = cbp_items.id'
	            		 , array( 'error' )
	            		 , $select::JOIN_LEFT
	            		);

            $select->where( array( 'cbp_items.folio_id' => $iFolioId ) );

            $select->order( 'process_start_time DESC' );

            // create a new result set based on the Box entity
            $resultSetPrototype = new ResultSet();

            $resultSetPrototype->setArrayObjectPrototype( new ItemEntity() );

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