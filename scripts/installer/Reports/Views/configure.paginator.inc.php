<?php

$sEntityType = 'boxes';

if( isset( $_GET[ 'entity_type' ] ) ){
	$sEntityType = $_GET[ 'entity_type' ];
}

$iPageNum    = '1';

if( isset( $_GET[ 'page_num' ] ) ){
	$iPageNum = (int) $_GET[ 'page_num' ];
}

$sNumItems    = '10';

if( isset( $_GET[ 'num_items' ] ) ){
	 $iNumItems = (int) $_GET[ 'num_items' ];
}

$iJobQueueId  = '';

if( isset( $_GET[ 'job_queue_id' ] ) ){
	$iJobQueueId = (int) $_GET[ 'job_queue_id' ];
}

$iBoxId  = '';

if( isset( $_GET[ 'box_id' ] ) ){
	$iBoxId = (int) $_GET[ 'box_id' ];
}

$sBoxNumber  = '';

if( isset( $_GET[ 'box_number' ] ) ){
	$sBoxNumber =  mysql_escape_string( $_GET[ 'box_number' ] );
}

$iFolioId  = '';

if( isset( $_GET[ 'folio_id' ] ) ){
	$iFolioId =  (int) $_GET[ 'folio_id' ];
}

$sFolioNumber  = '';

if( isset( $_GET[ 'folio_id' ] ) ){
	$sFolioNumber =  mysql_escape_string( $_GET[ 'folio_number' ] );
}

switch ( $sEntityType ){
	case 'jobs':
		$iId = null;
		break;
	case 'boxes':
		$iId = $iJobQueueId;
		break;
	case 'folios':
		$iId = $iBoxId;
		break;
	case 'items':
		$iId = $iFolioId;
		break;
}

$oPaginator   = $oTable->FetchAll( true, $iId );

$oPaginator->setCurrentPageNumber( $iPageNum );

$oPaginator->setItemCountPerPage( $sNumItems );

$oPages = $oPaginator->getPages();

























