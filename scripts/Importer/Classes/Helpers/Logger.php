<?php

namespace Classes\Helpers;

class Logger {

	private $sLogPath;
	private $sAdminEmail;
	private $sFolderLocation;
	private $sLogName;
	private $sErrorLogName;

	const LINE_WIDTH = 80;

	/*
	 * @var Zend_Log
	 */
	private $oInfoLogger;

	/*
	 * @var Zend_Log
	 */
	private $oExceptionLogger;

	public function __construct(  \Zend\Log\Logger	$oInfoLogger
								 ,\Zend\Log\Logger	$oExceptionLogger
								 ,                  $aSectionConfig ){

		$this->oInfoLogger		= $oInfoLogger;
		$this->oExceptionLogger	= $oExceptionLogger;
		$this->sLogPath         = $aSectionConfig[ 'path.logs' ];
		$this->sAdminEmail      = $aSectionConfig[ 'admin.email' ];

	}

	public function SetContext( $sContext ){

		$sContext = strtolower( $sContext );

		$sRootFolder 		= $this->sLogPath;
		$sFolderLocation 	= $sRootFolder . $sContext;

		if( !is_dir( $sFolderLocation ) ){
			if( !mkdir( $sFolderLocation, 0, true )) {
			    throw new Exceptions_Importer( 'Failed to create ' . $sFolderLocation );
			}
		}

		$this->sLogName      	= $this->CreateLogFileName( $sFolderLocation, '.log' );
		$this->sErrorLogName	= $this->CreateLogFileName( $sFolderLocation, '-error.log' );

		if( file_exists( $this->sLogName ) === false ){
			touch( $this->sLogName );
		}

		if( file_exists( $this->sErrorLogName ) === false ){
			touch( $this->sErrorLogName );
		}

		$oInfoWriter			= new \Zend\Log\Writer\Stream( $this->sLogName );
		$oExceptionWriter 		= new \Zend\Log\Writer\Stream( $this->sErrorLogName );

		$this->oInfoLogger->addWriter( $oInfoWriter );

		$this->oExceptionLogger->addWriter( $oExceptionWriter );

	}


	public function LogException( Exception $oException ){
		$sExceptionText = "\r\n" . $this->ComposeSectionHeader()
		                . 'Exception Logged : '     .$this->CreateFormattedDate() . "\r\n"
		                . $oException->getMessage() . "\r\n"
		                . $oException->getTraceAsString() . "\r\n\r\n";

		$this->MailException( $sExceptionText );

		$this->oExceptionLogger->log( $sExceptionText . "\r\n" , Zend\Log\Logger::INFO );
    }

    private function MailException ( $sExceptionText ){
    	if( substr( strtoupper( PHP_OS ), 0, 3 ) !== 'WIN' ){
			mail( $this->sAdminEmail, 'Exception', $sExceptionText );
    	}
    }


    private function ComposeSectionHeader(){
    	return str_repeat( '-', self::LINE_WIDTH )    . "\r\n";
    }


    private function CreateFormattedDate(){
    	return date( 'Y-m-d H:i:s' );
    }


    public function Step( $sName ){
    	$sLogText = $this->ComposeSectionHeader()
                  . $sName . ' : ' . $this->CreateFormattedDate() . "\r\n"
                  . $this->ComposeSectionHeader();

        $this->oInfoLogger->log( $sLogText . "\r\n" , Zend\Log\Logger::INFO );
    }


    public function Log( $sLogData ){
    	$this->oInfoLogger->log( $sLogData . "\r\n" , Zend\Log\Logger::INFO );
    }


    private function CreateLogFileName( $sLogName, $sExtension ) {
        return $sLogName . '/' . date( 'Y-m-d' ) . $sExtension;
    }
}