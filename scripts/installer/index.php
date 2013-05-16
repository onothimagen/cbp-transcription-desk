<?php

namespace Models;

error_reporting(E_ALL);

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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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