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
 * @subpackage Installer
 * @version 1.0
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  University College London
 */


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

if( $iId == ''){
	$iId = null;
}

$oPaginator   = $oTable->FetchAll( true, $iId );

$oPaginator->setCurrentPageNumber( $iPageNum );

$oPaginator->setItemCountPerPage( $sNumItems );

$oPages = $oPaginator->getPages();

























