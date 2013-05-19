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
 * @version 1.0
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  Ben Parish
 */

namespace Classes\Helpers;

use Classes\Exceptions\Importer as ImporterException;

class File {


	/*
	 * @param string $sDirectory
	 * @return void
	 */
	public function DeleteDirectory( $sDirectory ) {

	    $aFiles = scandir( $sDirectory );

	    if( !$aFiles ) {
	    	throw new ImporterException( 'DeleteDirectory(): unable to scan directory: ' . $sDirectory );
	    }

		foreach ( $aFiles as $sFile ){
			if ( $sFile != '.' && $sFile != '..'){

				$sPath = $sDirectory . '/' . $sFile;

				if( is_dir( $sPath ) ){
					$this->DeleteDirectory( $sPath );
				}

				if ( file_exists( $sPath ) ){

					if( !unlink( $sPath ) ) {
						throw new ImporterException( 'DeleteDirectory() unable to unlink ' . $sPath );
					}

				}
			}
		}

		if( is_dir( $sDirectory )){

			if( !rmdir( $sDirectory ) ) {
				throw new ImporterException( 'DeleteDirectory() unable to unlink ' . $sDirectory );
			}

		}
	}

	/*
	 * @param string $sDirectory
	 * @return void
	 */
	public function EmptyDirectory( $sDirectory ){

		$aFiles = scandir( $sDirectory );

		if( !$aFiles ) {
			throw new ImporterException( 'EmptyDirectory(): unable to scan directory: ' . $sDirectory );
		}

		foreach ( $aFiles as $sFile ){
			if ( $sFile != '.' && $sFile != '..'){

				$sFilePath = $sDirectory . '/' . $sFile;

				if( is_dir( $sFilePath ) ){
					continue;
				}

				unlink( $sFilePath );

				$bFileExists = file_exists( $sFilePath );

				if( $bFileExists ){
					throw new ImporterException( 'EmptyDirectory() unable to unlink ' . $sFilePath );
				}
			}
		}
	}


	/*
	 * @return resource
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
	 * @return boolean
	 */
	public function CheckDirExists( $sFilePath ){

		if( !is_dir( $sFilePath ) ) {

			if( !mkdir( $sFilePath, 0775, true )) {
				throw new ImporterException( 'Failed to create ' . $sFilePath );
			}
		}

		return true;

	}



	/*
	 * @return boolean
	 */
	public function CheckExists( $sName, $sFilePath ){

		if( file_exists( $sFilePath ) === false ){
			throw new ImporterException( $sName . ' ' . $sFilePath . ' does not exist' );
		}

		return true;

	}

	/*
	 * @param string $sDirectory
	 * @param string $sType Either 'directory' or 'file'
	 * @return
	 */
	public function ScanDirectory( $sDirectory, $sType ){

		if( $sType != 'directory' and $sType != 'file'){
			throw new ImporterException( '$sType passed to ScanDirectory() is not \'file or\' \'directory\'' );
		}

		$aItemList = array();

		$this->CheckDirExists( $sDirectory );

		$aItems  = scandir( $sDirectory );

		if( !$aItems ) {
			throw new ImporterException( 'ScanDirectory(): unable to scan directory: ' . $sDirectory );
		}

		foreach ( $aItems as $sItem ){

			if ( $sItem == '.' or $sItem == '..'){
				continue;
			}

			if( $sType === 'directory' and is_dir( $sDirectory . DIRECTORY_SEPARATOR . $sItem ) ){
				$aItemList[] = $sItem;
				continue;
			}

			if( $sType === 'file' and is_file( $sDirectory . DIRECTORY_SEPARATOR . $sItem ) ){
				$aItemList[] = $sItem;
			}
		}

		return $aItemList;

	}

   /*
    * @param string $sDirectory
	* @param string $sType Either 'directory' or 'file'
	* @return
	*/
	public function ScanImageDirectory( $sDirectory, $sBoxPrefix ){


		$this->CheckDirExists( $sDirectory );

		$aBoxList = array();

		$aBoxes   = scandir( $sDirectory );

		if( !$aBoxes ) {
			throw new ImporterException( 'ScanDirectory(): unable to scan directory: ' . $sDirectory );
		}

		foreach ( $aBoxes as $sBox ){

			if ( $sBox == '.' or $sBox == '..'){
				continue;
			}

			if( is_dir( $sDirectory . DIRECTORY_SEPARATOR . $sBox ) ){

				$sExpr = '/(' . $sBoxPrefix . '\d\d\d)\z/';

				$iMatch = preg_match( $sExpr , $sBox, $matches );

				if( $iMatch === 1  ){
					$aBoxList[] = $matches[1];
				}
			}
		}

		return $aBoxList;
	}

	/*
	 * @param int $iPid
	 * @return void;
	 * @todo Parse the output for verification
	 */
	public function KillProcess( $iPid ){

		if( $this->ServerOS() === 'LINUX'){
			exec( 'kill ' . $iPid );
			return;
		}
		exec( 'taskkill /PID ' . $iPid );
		return;
	}



	/*
	 * @link http://ionfist.com/php/start-stop-process-from-php/
	 * @link http://stackoverflow.com/questions/1656350/php-check-process-id
	 *
	 * @return boolean
	 */
    public function ProcessExists( $iPid ){

        if( $this->ServerOS() === 'LINUX'){
        	exec( 'ps ' . $iPid, $aState );
			if( ( count( $aState ) >= 2 ) ){
				return true;
			}
			return false;
        }

        $iPid = (int) $iPid;

        $aProcesses = explode( "\n", shell_exec( "tasklist.exe" ));

        foreach( $aProcesses as $sProcess ){

             if( strpos( $sProcess, 'php' ) === 0 || strpos( $sProcess, "===" ) === 0 ){
                  continue;
             }

             $aMatches = false;

             if( preg_match( "/(.*)\s+(\d+).*$/", $sProcess ) ){

                $iRunningPid = $aMatches[ 2 ];

                if( $iPid === (int) $iRunningPid ){
                    return true;
                }
             }
        }
        return false;
    }



    /*
     *@return string
    */
    public function ServerOS(){

    	$sSystem = strtoupper( PHP_OS );

    	if( substr( $sSystem, 0, 3 ) == 'WIN' ){
    		return 'WIN';
    	}elseif( $sSystem == 'LINUX' ){
    		return 'LINUX';
    	}else{
    		return 'OTHER';
    	}

    }


}












































