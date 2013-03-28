<?php

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

switch ( $_SERVER['HTTP_HOST'] ){
	case 'www.transcribe-bentham.da.ulcc.ac.uk':
		$sConfigSection = 'production';
		break;
	case 'w02.benpro.wf.ulcc.ac.uk':
		$sConfigSection = 'production';
		break;
	case 'cbp-transcription-desk.local':
		error_reporting(E_ALL);
		ini_set( 'display_errors', 1 );
		$sConfigSection = 'development';
		break;
}


$oConfig       = new Zend\Config\Reader\Ini();

$oConfig->setNestSeparator( ' : ' );

$aConfig        = $oConfig->fromFile( 'config.ini.php' );

$aSectionConfig = $aConfig[ $sConfigSection ][ 'common' ];

$oInfoLogger		= new Zend\Log\Logger;
$oExceptionLogger	= new Zend\Log\Logger;


/*****************************************
 * CONFIGURE ADAPTER
*****************************************/

$aDbConfig	= array(
					 'driver'	=> $aConfig[ 'common' ][ 'database.adapter' ]
					,'host'     => $aConfig[ 'common' ] ['database.params.host' ]
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
																	  , 'aSectionConfig'   => $aSectionConfig
									 							  )
									 );

$oDi->instanceManager()->setParameters( 'Classes\Db\JobQueue', array( 'oAdapter' => $oAdapter ));
$oDi->instanceManager()->setParameters( 'Classes\Db\MetaData', array( 'oAdapter' => $oAdapter ));
$oDi->instanceManager()->setParameters( 'Classes\Db\Item'    , array( 'oAdapter' => $oAdapter ));

/*****************************************
 * TASKS
*****************************************/

require_once 'Classes/TaskAbstract.php';


/*****************************************
 * SETUP FOR TESTING
*****************************************/

$oMetaDataDb = new Classes\Db\MetaData( $oAdapter );
$oItemDb     = new Classes\Db\Item( $oAdapter );

$oFile       = new Classes\Helpers\File();

$oItemDb->Truncate();
$oMetaDataDb->Truncate();

$sImageDir = $aSectionConfig[ 'path.image.export' ] . '\001';

if( file_exists( $sImageDir ) ){
	$oFile->DeleteDirectory( $sImageDir );
}
















































