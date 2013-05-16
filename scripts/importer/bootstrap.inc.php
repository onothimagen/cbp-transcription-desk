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

chdir( $sImporterPath );

$sScriptPath   =  substr( $sImporterPath, 0 , strrpos( $sImporterPath, DIRECTORY_SEPARATOR ) );

$sIncludePath  =  $sImporterPath . PATH_SEPARATOR . $sScriptPath . PATH_SEPARATOR;

set_include_path( get_include_path() . PATH_SEPARATOR . $sIncludePath );

require 'Zend/Loader/StandardAutoloader.php';

$autoloader = new Zend\Loader\StandardAutoloader( array( 'autoregister_zf' => true ) );

$sImporterClassNameSpacePath = $sImporterPath . DIRECTORY_SEPARATOR . 'Classes';

$sInstallerNameSpacePath     = $sScriptPath . DIRECTORY_SEPARATOR . 'installer' . DIRECTORY_SEPARATOR . 'Reports' . DIRECTORY_SEPARATOR;

$autoloader->registerNamespace( 'Classes'
							  , $sImporterClassNameSpacePath
							  , $sInstallerNameSpacePath );

$autoloader->register();



/*****************************************
 * LOAD CONFIGURATION
 *****************************************/


$oConfig       = new Zend\Config\Reader\Ini();

$oConfig->setNestSeparator( ' : ' );

$aConfig        = $oConfig->fromFile( 'config.ini.php' );

// First attempt to load relevant section based on host name

if( isset ( $_SERVER['HTTP_HOST' ] ) ){

	$sServerHost = $_SERVER['HTTP_HOST' ];

	foreach ( $aConfig as $sSection => $aConfigSection ){

		if(  isset( $aConfigSection['common'] ) and isset( $aConfigSection[ 'common' ][ 'host' ] ) ){

			$sHost = $aConfigSection[ 'common' ][ 'host' ];

			if( $sHost != '' and $sHost === $sServerHost ){
				$sConfigSection = $sSection;
				break;
			}
		}

	}

}

// Override this with 'active.environment' is it has been set.
// Set the 'active.environment' when running PHP CLI

if( isset( $aConfig['common'][ 'active.environment' ] ) and $aConfig['common'][ 'active.environment' ] !== '' ){
	$sConfigSection = $aConfig['common'][ 'active.environment' ];
}

// Load common config items

$aCommonConfig = $aConfig[ 'common' ];

if( isset( $sConfigSection [ $aConfig ] ) === false ){
	throw new Exception( $sConfigSection . ' does not exist in config.ini.php' );
}


// Load section specific config items

$aSectionConfig = array_merge( $aCommonConfig, $aConfig[ $sConfigSection ][ 'common' ] );

// Append the path prefix to the paths if there is one

if( isset ( $aSectionConfig[ 'path.prefix' ] ) and  $aSectionConfig[ 'path.prefix' ] != '' ){

	$sPathPrefix = $aSectionConfig[ 'path.prefix' ];

	foreach ( $aSectionConfig as $sConfig => $sConfigValue ){

		if( substr( $sConfig, 0,4 ) == 'path' and $sConfig != 'path.prefix' ){
			$aSectionConfig[ $sConfig ] = $sPathPrefix . $sConfigValue;
		}
	}
}


/*****************************************
 * CONFIGURE LOGGER
 *****************************************/


$oInfoLogger		= new Zend\Log\Logger;
$oExceptionLogger	= new Zend\Log\Logger;

$oLogger            = new Classes\Helpers\Logger( $oInfoLogger, $oExceptionLogger, $aSectionConfig );

$oLogger->SetContext( 'jobs' );


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


$oDi->instanceManager()->setParameters( 'Classes\Db\JobQueue',  array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\Box'     ,  array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\Folio'   ,  array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\Item'    ,  array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\ErrorLog',  array( 'oAdapter' => $oAdapter ) );
$oDi->instanceManager()->setParameters( 'Classes\Db\MediaWiki', array( 'oAdapter' => $oAdapter ) );

$oMediaWikiDb = $oDi->get( 'Classes\Db\MediaWiki' );

$oDi->instanceManager()->setParameters( 'Classes\Mappers\JobItemsToMwXml', array( $oMediaWikiDb, 'aConfig' => $aSectionConfig ) );

/*****************************************
 * ADD TASK ABSTRACT
*****************************************/

require_once 'Classes/TaskAbstract.php';
















































