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

namespace Models;

ini_set( 'display_errors', 'On' );

use \Zend\Paginator\Paginator;

use \Classes\Entities\JobQueue;

require_once '../importer/bootstrap.inc.php';

require_once 'Reports/Models/TableAbstract.php';

$sInstallerPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'Reports'  . DIRECTORY_SEPARATOR;

set_include_path( get_include_path() . PATH_SEPARATOR . $sInstallerPath . PATH_SEPARATOR );

function GetEntityType(){

	if( isset( $_GET[ 'entity_type' ] ) ){
		return $_GET[ 'entity_type' ];
	}
	return 'jobs';
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>Installer</title>
	<script type="text/javascript" src="/w/resources/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/installer.js"></script>
	<script type="text/javascript" src="js/jquery.qtip.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/installer.css" />
    <link rel="stylesheet" type="text/css" href="css/jquery.qtip.min.css" />
  </head>
  <body>

	<div id="console"></div>
	<div id="jobcontrols">
	    <button id="runjob" type="button" style="" onclick="ajaxConnect( 'start', null );StartScroll();">Run new job</button>
	    <div id="loader"><img src="images/loader.gif" />&nbsp;&nbsp;&nbsp;&nbsp;Processing Job...</div>
    </div>

    <div id="table-section">
    <?php

    $sEntityType = 'jobs';

    $sEntityType = ucfirst( GetEntityType() );

    require_once 'Reports/Views/' . $sEntityType . '.phtml';

    ?>
    </div>
  </body>
</html>