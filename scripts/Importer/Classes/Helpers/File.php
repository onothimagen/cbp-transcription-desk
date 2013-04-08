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

class File {

	/*
	 *
	 */
	public function Write( $sFile, $sData ){

		if( trim( $sFile ) === '' ){
			throw new \Classes\Exceptions\Importer( '$sFile passed to Write() is empty' );
		}

		if( trim( $sData ) === '' ){
			throw new \Classes\Exceptions\Importer( '$sData passed to Write() is empty' );
		}

		$bBytesWritten = file_put_contents( $sFile, $sData );

		if ( $bBytesWritten !== true ) {
			throw new \Classes\Exceptions\Importer( 'Cannot write $sData to ' . $sFile );
		}

		if( file_exists( $sFile ) === false ){
			throw new \Classes\Exceptions\Importer( 'Failed to create $sFile: ' . $sFile );
		}

	}

	/*
	 *
	 */
	public function Decompress( $sCompressedFileName ){

		// Initiate buffer size for reading process
		$sBufferSize = 4096;

		// Remove the .gz extension from source file name
		$sDecompressedFileName = str_replace( '.gz', '', $sCompressedFileName );

		// Create empty file to hold uncompressed data
		$hOutPutFile = fopen( $sDecompressedFileName, 'wb' );
		if( $hOutPutFile === false ){
			throw new \Classes\Exceptions\Importer( 'fopen() failed to open ' . $sDecompressedFileName );
		}

		// Read from the source file, uncompress, and write to output file
		$hInputFile = gzopen( $sCompressedFileName, 'rb' );
		if( $hInputFile === false ){
			throw new \Classes\Exceptions\Importer( 'gzopen() failed to open ' . $sCompressedFileName );
		}


		while( !gzeof( $hInputFile ) ) {

			$sReadData = gzread( $hInputFile, $sBufferSize );
			if( $sReadData === false ){
				throw new \Classes\Exceptions\Importer( 'gzread() failed to read ' . $sCompressedFileName );
			}

		    $bWriteResult = fwrite( $hOutPutFile, $sReadData );
		    if( $bWriteResult === false ){
		    	throw new \Classes\Exceptions\Importer( 'fwrite() failed to write data to  ' . $sDecompressedFileName );
		    }
		}


		// Terminate gzip and file handler
		fclose( $hOutPutFile );
		gzclose( $hInputFile );

		if( file_exists( $sDecompressedFileName ) === false ){
			throw new \Classes\Exceptions\Importer( 'Failed to decompress to ' . $sDecompressedFileName );
		}
		return $sDecompressedFileName;

	}

	/*
	 *
	 */
	public function WriteDataToFile(  $sSourceLocation
									 ,$sTargetLocation
									 ,$sData ){

		if( file_exists( $sTargetLocation ) ){
			return;
		}

		if( file_exists( $sSourceLocation ) ){
			return;
		}

		$this->Write( $sSourceLocation, $sData );

	}

	/*
	 *
	 */
	public function WriteDataToTargetFile( 	 $sSourceLocation
											,$sTargetLocation
											,$sData ){

		if( file_exists( $sTargetLocation ) ){
			return true;
		}

		$this->CheckExists( $sSourceLocation );

		if( $sData === false ){
			$bCopyResult = copy( $sSourceLocation, $sTargetLocation );
			if( $bCopyResult == false ){
				throw new \Classes\Exceptions\Importer( 'Unable to copy ' . $sSourceLocation . ' to ' . $sTargetLocation );
			}
			return true;
		}

		$this->Write( $sTargetLocation, $sData);


	}

	/*
	 *
	 */
	public function CopyFilesOver( $sSrcDirectory, $sTargetDirectory ){

		$aFiles = scandir( $sSrcDirectory );

		foreach ( $aFiles as $sFile ){

			if ( $sFile != '.' && $sFile != '..' ){

				$sSourceFile = $sSrcDirectory . $sFile;
				$sTargetFile = $sTargetDirectory . $sFile;

				if( file_exists( $sTargetFile ) OR is_dir( $sSourceFile ) ){
					continue;
				}

				copy( $sSourceFile, $sTargetFile );
			}

		}

	}

	/*
	 *
	 */
	public function MoveFilesOver( $sSrcDirectory, $sTargetDirectory ){

		$aFiles = scandir( $sSrcDirectory );

		foreach ( $aFiles as $sFile ){

			if ( $sFile != '.' && $sFile != '..'){

				$sSrcFile		= $sSrcDirectory . '/' . $sFile;
				$sTargetFile 	= $sTargetDirectory . $sFile;

				if( is_dir( $sSrcFile ) ){
					continue;
				}

				rename( $sSrcFile, $sTargetFile );
			}

		}

	}

	/*
	 *
	 */
	public function CompressAndArchiveDirectory( $sDirectoryToBeArchived, $sArchiveLocation ){

		$sDate	= date('Y-m-d');
		$sOS 	= $this->ServerOS();

		if( $sOS == 'WIN' ){
			$sArchiveLocation = $sArchiveLocation . $sDate . '.zip';
			 	$sScript = '"C:\Program Files (x86)\7-Zip\7z" a "' . $sArchiveLocation . '" "'. $sDirectoryToBeArchived . '"';
		}

		if(  $sOS == 'LINUX' ){
			$sArchiveLocation	= $sArchiveLocation . $sDate . '.tgz';
			$sScript 			= 'tar  -C ' . $sDirectoryToBeArchived . ' -zcvf ' . $sArchiveLocation . '  --exclude="./archive" .';

		}

		$results	= system( $sScript, $sRetval );

		if( !file_exists( $sArchiveLocation ) ){
			throw new \Classes\Exceptions\Importer( ' Failed to create ArchiveLocation: ' . $sArchiveLocation );
		}

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
		rmdir( $sDirectory );
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

		$this->CheckExists( $sFilePath );

		$hHandle = fopen( $sFilePath, 'r' );

		if( $hHandle === false ){
			throw new \Classes\Exceptions\Importer( 'Cannot open ' . $sFilePath );
		}

		return $hHandle;

	}

	/*
	 *
	 */
	public function CheckExists( $sFilePath ){

		$bFileExists = file_exists( $sFilePath );

		if( $bFileExists === false ){
			throw new \Classes\Exceptions\Importer( $sFilePath . ' does not exist<p />' );
		}

	}


}












































