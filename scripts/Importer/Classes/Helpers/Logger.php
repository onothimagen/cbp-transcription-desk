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
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  Ben Parish
 */

namespace Classes\Helpers;

use Classes\Exceptions\Importer as ImporterException;

use Zend\Log\Logger         as ZendLogger;
use Zend\Log\Writer\Stream  as Stream;

class Logger {

	private $sLogPath;
	private $sAdminEmail;
	private $sFolderLocation;
	private $sLogName;
	private $sErrorLogName;
	private $sSystemGroup;

	const LINE_WIDTH = 80;

	/* @var ZendLogger */
	private $oInfoLogger;

	/* @var ZendLogger */
	private $oExceptionLogger;

	public function __construct(   ZendLogger $oInfoLogger
								 , ZendLogger $oExceptionLogger
								 ,            $aConfig ){

		$this->oInfoLogger		= $oInfoLogger;
		$this->oExceptionLogger	= $oExceptionLogger;
		$this->sLogPath         = $aConfig[ 'path.logs' ];
		$this->sAdminEmail      = $aConfig[ 'admin.email' ];
		$this->sSystemGroup     = $aConfig[ 'system.group' ];

	}

	public function SetContext( $sContext ){

		$sContext = strtolower( $sContext );

		$sRootFolder 		= $this->sLogPath;
		$sFolderLocation 	= $sRootFolder . DIRECTORY_SEPARATOR . $sContext;

		if( !is_dir( $sFolderLocation ) ){
			if( !mkdir( $sFolderLocation, 0, true )) {
			    throw new ImporterException( 'Failed to create ' . $sFolderLocation );
			}
		}

		$this->sLogName      	= $this->CreateLogFileName( $sFolderLocation, '.log' );
		$this->sErrorLogName	= $this->CreateLogFileName( $sFolderLocation, '-error.log' );

		if( file_exists( $this->sLogName ) === false ){
			touch( $this->sLogName );
			if(  $this->ServerOS() === 'UNIX' ){
				chmod( $this->sLogName, 0770 );
				chgrp( $this->sLogName, $this->sSystemGroup );
			}
		}

		if( file_exists( $this->sErrorLogName ) === false ){
			touch( $this->sErrorLogName );
			if(  $this->ServerOS() === 'UNIX' ){
				chmod( $this->sErrorLogName, 0770 );
				chgrp( $this->sErrorLogName, $this->sSystemGroup );
			}
		}

		$oInfoWriter			= new Stream( $this->sLogName );
		$oExceptionWriter 		= new Stream( $this->sErrorLogName );

		$this->oInfoLogger->addWriter( $oInfoWriter );

		$this->oExceptionLogger->addWriter( $oExceptionWriter );

	}


	public function LogException( ImporterException $oException ){
		$sExceptionText = "\r\n" . $this->ComposeSectionHeader()
		                . 'Exception Logged : '     .$this->CreateFormattedDate() . "\r\n"
		                . $oException->getMessage() . "\r\n"
		                . $oException->getTraceAsString() . "\r\n\r\n";

		$this->MailException( $sExceptionText );

		$this->oExceptionLogger->log( ZendLogger::ERR, $sExceptionText . "\r\n" );

    }

    private function MailException ( $sExceptionText ){
    	if( $this->sAdminEmail !== '' ){
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

        $this->oInfoLogger->log( ZendLogger::INFO, $sLogText . "\r\n" );
    }


    public function Log( $sLogData ){
    	$this->oInfoLogger->log( $sLogData . "\r\n" , ZendLogger::INFO );
    }


    private function CreateLogFileName( $sLogName, $sExtension ) {
        return $sLogName . '/' . date( 'Y-m-d' ) . $sExtension;
    }

    /*
     *
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






































