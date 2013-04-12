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


/*****************************************
 * CONFIGURE AUTOLOADING
*****************************************/

$sImporterPath = dirname( __FILE__ );

$sScriptPath   =  substr($sImporterPath, 0 , strrpos( $sImporterPath, DIRECTORY_SEPARATOR ) );

$sIncludePath  =  $sImporterPath . PATH_SEPARATOR . $sScriptPath . PATH_SEPARATOR;

set_include_path( get_include_path() . PATH_SEPARATOR . $sIncludePath );


require 'Zend/Loader/StandardAutoloader.php';

$autoloader = new Zend\Loader\StandardAutoloader( array( 'autoregister_zf' => true ) );

$sClassNameSpacePath = $sImporterPath . '/Classes';


$autoloader->registerNamespace( 'Classes'
							  , $sClassNameSpacePath );

$autoloader->register();



/*****************************************
 * CONFIGURE LOGGER
 *****************************************/

// Set the $sConfigSection if running from cron

$sConfigSection = 'staging';

if( isset ( $_SERVER[ 'SCRIPT_FILENAME' ]) ){

	switch ( $_SERVER['HTTP_HOST'] ){
		case 'www.transcribe-bentham.da.ulcc.ac.uk':
			$sConfigSection = 'production';
			break;
		case 'w02.benpro.wf.ulcc.ac.uk':
			error_reporting(E_ALL);
			ini_set( 'display_errors', 1 );
			$sConfigSection = 'staging';
			break;
		case 'cbp-transcription-desk.local':
			error_reporting(E_ALL);
			ini_set( 'display_errors', 1 );
			$sConfigSection = 'development';
			break;
	}

}


$oConfig       = new Zend\Config\Reader\Ini();

$oConfig->setNestSeparator( ' : ' );

$aConfig        = $oConfig->fromFile( 'config.ini.php' );

// Load common config items

$aSectionConfig = $aConfig[ 'common' ];


// Load section specific config items

$aSectionConfig = array_merge( $aSectionConfig, $aConfig[ $sConfigSection ][ 'common' ] );


$oInfoLogger		= new Zend\Log\Logger;
$oExceptionLogger	= new Zend\Log\Logger;


/*****************************************
 * CONFIGURE ADAPTER
*****************************************/

$aDbConfig	= array(
					 'driver'	=> $aSectionConfig[ 'database.adapter' ]
					,'host'     => $aSectionConfig[ 'database.params.host' ]
					,'username' => $aSectionConfig[ 'database.params.username' ]
					,'password'	=> $aSectionConfig[ 'database.params.password' ]
					,'dbname'   => $aSectionConfig[ 'database.params.dbname' ]
					);

$oAdapter = new Zend\Db\Adapter\Adapter( $aDbConfig );


/*****************************************
 * CONFIGURE DIC
*****************************************/


$oDi = new Zend\Di\Di();

$oDi->instanceManager()->setParameters( 'Classes\Helpers\Logger', array(
										    							'oInfoLogger'      => $oInfoLogger
																   	  , 'oExceptionLogger' => $oExceptionLogger
																	  , 'aConfig'          => $aSectionConfig
									 							       )
									 );

$oDi->instanceManager()->setParameters( 'Classes\Mappers\JobItemsToMwXml', array( 'aConfig' => $aSectionConfig ) );

$oDi->instanceManager()->setParameters( 'Classes\Db\JobQueue', array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\Box'     , array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\Folio'   , array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\Item'    , array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\ErrorLog', array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\MediaWiki', array( 'oAdapter' => $oAdapter ) );

/*****************************************
 * TASKS
*****************************************/

require_once 'Classes/TaskAbstract.php';


if( $sConfigSection === 'development' or $sConfigSection === 'staging' ){

 require 'test_scripts.inc.php';

}














































