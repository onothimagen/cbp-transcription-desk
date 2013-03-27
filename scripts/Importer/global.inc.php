<?php

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

/*****************************************
 * CONFIGURE LOGGER
 *****************************************/

$oConfig       = new Zend\Config\Reader\Ini();

$oConfig->setNestSeparator( ' : ' );

$aData         = $oConfig->fromFile( 'config.ini.php' );

$sLogLocation  = $aData[ $sConfigSection ]['common']['loglocation'];


$oInfoLogger		= new Zend\Log\Logger;
$oExceptionLogger	= new Zend\Log\Logger;


$oLogger	   = new Classes\Helpers\Logger(   $oInfoLogger
											 , $oExceptionLogger
											 , $sLogLocation );

/*****************************************
 * CONFIGURE ADAPTERS
*****************************************/

$aDbConfig	= array(
					 'driver'	=> $aData['common']['database.adapter']
					,'host'     => $aData['common']['database.params.host']
					,'username' => $aData[ $sConfigSection ]['common']['database.params.username']
					,'password'	=> $aData[ $sConfigSection ]['common']['database.params.password']
					,'dbname'   => $aData[ $sConfigSection ]['common']['database.params.dbname']
					);

$oAdapter = new Zend\Db\Adapter\Adapter( $aDbConfig );

$oMetaDataDb        = new Classes\Db\MetaData( $oAdapter );
$oItemDb			= new Classes\Db\Item( $oAdapter );


/*****************************************
 * CONFIGURE HELPERS
*****************************************/

$oFileHelper		= new Classes\Helpers\File();


/*****************************************
 * CONFIGURE DI
*****************************************/


$oDi = new Zend\Di\Di();

$oDi->instanceManager()->setParameters( 'Classes\Helpers\Logger', array(
										    							'oInfoLogger'      => $oInfoLogger
																   	  , 'oExceptionLogger' => $oExceptionLogger
																	  , 'sLogLocation'     => $sLogLocation
									 							  )
									 );

$oDi->instanceManager()->setParameters( 'Classes\Db\JobQueue', array( 'oAdapter' => $oAdapter ));
$oDi->instanceManager()->setParameters( 'Classes\Db\MetaData', array( 'oAdapter' => $oAdapter ));
$oDi->instanceManager()->setParameters( 'Classes\Db\Item', array( 'oAdapter' => $oAdapter ));


/*****************************************
 * SETUP FOR TESTING
*****************************************/

$oItemDb->Truncate();
$oMetaDataDb->Truncate();
















































