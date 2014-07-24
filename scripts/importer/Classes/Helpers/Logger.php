<?php

/**
 * Copyright (C) Ben Parish
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
 * @version 1.0
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  Ben Parish
 * @todo The error logger creates an empty file whether or not there any errors. Someway to clean up is needed
 */

namespace Classes\Helpers;

use Classes\Exceptions\Importer as ImporterException;

use Zend\Log\Logger         as ZendLogger;
use Zend\Log\Writer\Stream  as Stream;

class Logger {

	private $sLogPath;
	private $sAdminEmail;
	private $sFileGroup;
	private $sFolderLocation;
	private $sLogName;
	private $sErrorLogName;
	private $sSystemGroup;

	const LINE_WIDTH = 80;

	/* @var ZendLogger */
	private $oInfoLogger;

	/* @var ZendLogger */
	private $oExceptionLogger;

    private $sContext;

	private $iJobQueueId;

	/*
	 * @param ZendLogger $oInfoLogger
	 * @param ZendLogger $oExceptionLogger
	 * @param string[] $aConfig
	 * @return void
	 */
	public function __construct(   ZendLogger $oInfoLogger
								 , ZendLogger $oExceptionLogger
								 ,            $aConfig ){

		$this->oInfoLogger		= $oInfoLogger;
		$this->oExceptionLogger	= $oExceptionLogger;
		$this->sLogPath         = $aConfig[ 'path.logs' ];
		$this->sAdminEmail      = $aConfig[ 'admin.email' ];
		$this->sFileGroup       = $aConfig[ 'file.group' ];

	}

	/*
	 * Configures the logger writers, using the context (process) to determine the output file's directory
	 *
	 * @param string $sContext
	 * @param integer $iJobQueueId|null
	 * @return void
	 */
	public function ConfigureLogger( $sContext, $iJobQueueId = null ){

        // We don't want to keep adding identical writers

        if( $this->sContext === $sContext and $this->iJobQueueId === $iJobQueueId ){
            return;
        }

        $this->sContext    = $sContext;

		$this->iJobQueueId = $iJobQueueId;

		$sContext          = strtolower( $sContext );

		$sContext          = str_replace( ' ', '_', $sContext );

		$sRootFolder 	   = $this->sLogPath;
		$sFolderLocation   = $sRootFolder . DIRECTORY_SEPARATOR . $sContext;

		$sFileGroup        = $this->sFileGroup;

		if( !is_dir( $sFolderLocation ) ){

			if( !mkdir( $sFolderLocation, 0775, true )) {
			    throw new ImporterException( 'Failed to create ' . $sFolderLocation );
			}

			chgrp( $sFolderLocation, $sFileGroup );

		}

		$this->sLogName      	= $this->CreateLogFileName( $sFolderLocation, '.log' );
		$this->sErrorLogName	= $this->CreateLogFileName( $sFolderLocation, '-error.log' );

		if( file_exists( $this->sLogName ) === false ){
			touch( $this->sLogName );
			chmod( $this->sLogName, 0664 );
			chgrp( $this->sLogName, $sFileGroup );
		}

		if( is_writable( $this->sLogName ) === false ){
			throw new ImporterException( 'The info logging file ' . $this->sLogName . ' is not writeable' );
		}

		if( file_exists( $this->sErrorLogName ) === false ){
			touch( $this->sErrorLogName );
			chmod( $this->sErrorLogName, 0664 );
			chgrp( $this->sErrorLogName, $sFileGroup );
		}

		if( is_writable( $this->sErrorLogName ) === false ){
			throw new ImporterException( 'The error logging file ' . $this->sErrorLogName . ' is not writeable' );
		}

		$oInfoWriter			= new Stream( $this->sLogName );
		$oExceptionWriter 		= new Stream( $this->sErrorLogName );

		$this->oInfoLogger->addWriter( $oInfoWriter );

		$this->oExceptionLogger->addWriter( $oExceptionWriter );

	}


	/*
	 * Formats the exception, mails it and writes it to the log
	 *
	 * @param ImporterException $oException
	 * @return void
	 */
	public function LogException( ImporterException $oException ){

		$sExceptionText = "\r\n" . $this->ComposeSectionHeader()
		                . 'Exception Logged : '     .$this->CreateFormattedDate() . "\r\n"
		                . $oException->getMessage() . "\r\n"
		                . $oException->getTraceAsString() . "\r\n\r\n";

		$this->MailException( $sExceptionText );

		$this->oExceptionLogger->log( ZendLogger::ERR, $sExceptionText . "\r\n" );

    }



    /*
     * @param string $sExceptionText
     * @return void
     */
    private function MailException ( $sExceptionText ){
    	if( $this->sAdminEmail !== '' ){
			mail( $this->sAdminEmail, 'Exception', $sExceptionText );
    	}
    }



    /*
     * Hypehen ruler for highlighting
     *
     * @return string
     */
    private function ComposeSectionHeader(){
    	return str_repeat( '-', self::LINE_WIDTH )    . "\r\n";
    }


    /*
     * @return string
     */
    private function CreateFormattedDate(){
    	return date( 'Y-m-d H:i:s' );
    }


    /*
     * A step is a higher level reporting step and formatted with section header highlighting -----
     *
     * @param string $sStep
     */
    public function Step( $sStep ){

    	$sLogText =  "\r\n" . $this->ComposeSectionHeader()
                      . $sStep . ' : ' . $this->CreateFormattedDate() . "\r\n"
                      . $this->ComposeSectionHeader();

        $this->Log( $sLogText );

        return;

    }

	/*
	 * Formats for output depending on whether via the browser
	 *
	 * @param string $sStep
	 * @return void
	 */
    public function OutPutToDisplay( $sStep ){

    	// Only needed if running in the browser

    	if( isset ( $_SERVER[ 'HTTP_HOST' ]) ){

    		$sStep = str_replace("\r\n", '<br />', $sStep );
			$sStep = str_replace("\n", '<br />', $sStep );
			echo $sStep . '<br />';

	    	//Ensure browser's minimal character chunk for display is reached
	    	echo str_pad( '', 4096 ) . "\n";
	    	ob_flush();
	    	flush();  // needed ob_flush

    	}else{
	    	echo $sStep . "\n";
	    	echo str_pad( '', 4096 ) . "\n";
    	}

    }

	/*
	 * Write to file and output to display
	 *
	 * @param string $sLogData
	 */
    public function Log( $sLogData ){

    	$this->oInfoLogger->log( ZendLogger::INFO, $sLogData . "\r\n" );
        $this->OutPutToDisplay( $sLogData );
    }


    /*
     * @param string $sLogName
     * @param string $sExtension
     * @return string
     */
    private function CreateLogFileName( $sLogName, $sExtension ) {

    	$iJobQueueId = $this->iJobQueueId;

    	if( $iJobQueueId !== null ){
    		return $sLogName . '/' . $iJobQueueId . $sExtension;
    	}

        return $sLogName . '/' . date( 'Y-m-d' ) . $sExtension;
    }


    /*
     * @return string
    */
    private function ServerOS(){

    	$sys = strtoupper( PHP_OS );

    	if( substr( $sys, 0, 3 ) == 'WIN' ){
    		return 'WIN';
    	}elseif( $sys == 'LINUX' ){
    		return 'LINUX';
    	}else{
    		return 'OTHER';
    	}

    }

}






































