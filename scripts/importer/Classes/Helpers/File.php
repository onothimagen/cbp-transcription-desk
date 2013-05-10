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
 * @package CBP Transcriptions
 * @subpackage Importer
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  Ben Parish
 */

namespace Classes\Helpers;

use Classes\Exceptions\Importer as ImporterException;

class File {


	/*
	 *
	 */
	public function DeleteDirectory( $sDirectory ) {

	    $aFiles = scandir( $sDirectory );

		foreach ( $aFiles as $sFile ){
			if ( $sFile != '.' && $sFile != '..'){

				$sPath = $sDirectory . '/' . $sFile;

				if( is_dir( $sPath ) ){
					$this->DeleteDirectory( $sPath );
				}

				if ( file_exists( $sPath ) ){
					unlink( $sPath );
				}
			}
		}

		if( is_dir( $sDirectory )){
			rmdir( $sDirectory );
		}
	}

	/*
	 *
	 */
	public function EmptyDirectory( $sDirectory ){

		$aFiles = scandir( $sDirectory );

		foreach ( $aFiles as $sFile ){
			if ( $sFile != '.' && $sFile != '..'){

				$sPath = $sDirectory . '/' . $sFile;

				if( is_dir( $sPath ) ){
					continue;
				}

				unlink( $sPath );
			}
		}
	}


	/*
	 *
	*/
	public function GetFileHandle( $sFilePath ){

		$this->CheckExists( 'FilePath', $sFilePath );

		$hHandle = fopen( $sFilePath, 'r' );

		if( $hHandle === false ){
			throw new ImporterException( 'Cannot open ' . $sFilePath );
		}

		return $hHandle;

	}

	/*
	 *
	 */
	public function CheckDirExists( $sName, $sFilePath ){

		$bFileExists = file_exists( $sFilePath );

		if( $bFileExists === false ){
			mkdir( $sFilePath );
		}

	}

	/*
	 *
	 */
	public function CheckExists( $sName, $sFilePath ){

		$bFileExists = file_exists( $sFilePath );

		if( $bFileExists === false ){
			throw new ImporterException( $sName . ' ' . $sFilePath . ' does not exist<p />' );
		}

	}

    public function PidExists( $iPid ){

        if( $this->ServerOS() === 'LINUX'){
            return file_exists( '/proc/' . $iPid );
        }

        $iPid = (int) $iPid;

        $aProcesses = explode( "\n", shell_exec( "tasklist.exe" ));

        foreach( $aProcesses as $process ){

             if( strpos( $process, 'php' ) === 0 || strpos( $process, "===" ) === 0 ){
                  continue;
             }

             $aMatches = false;

             if( preg_match( "/(.*)\s+(\d+).*$/", $process ) ){
                $iRunningPid = $aMatches[ 2 ];
                if( $iPid === (int) $iRunningPid ){
                    return true;
                }
             }
        }
        return false;
    }

    /*
     *
    */
    public function ServerOS(){

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












































