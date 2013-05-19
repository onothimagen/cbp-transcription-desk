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
 * @version 1.0
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  University College London
 */

namespace Classes;

use Zend\Di\Di;

use Zend\Db\ResultSet\ResultSet;

use Classes\Db\JobQueue  as JobQueueDb;
use Classes\Db\Box       as BoxDb;
use Classes\Db\Folio     as FolioDb;
use Classes\Db\Item      as ItemDb;


use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Box      as BoxEntity;
use Classes\Entities\Folio    as FolioEntity;
use Classes\Entities\Item     as ItemEntity;

use Classes\Exceptions\Importer as ImporterException;

/*
 * This class moves all processed images and the XML file to the
 * specified archive directory
 */
class ArchiveTask  extends TaskAbstract{

	/* @var FolioEntity */
	private $oFolioItemEntity;

    private $sJobArchivePath;

	private $sArchivePath;

    private $sXmlPath;

    private $sXMLExportPath;

    private $sBoxPrefix;

    private $sImageImportPath;

    private $sBoxNumber;

    private $sTokenSeperator;

   /*
    * @param Di             $oDi
    * @param string[]       $aConfig
    * @param JobQueueEntity $oJobQueueEntity
    * @return void
    */
	public function __construct(  Di             $oDi
								,                $aConfig
								, JobQueueEntity $oJobQueueEntity ){

		parent::__construct( $oDi );

        $this->sProcess         = 'archive';

        $this->sPreviousProcess = 'verify';

        $this->sArchivePath     = $aConfig[ 'path.archive' ];

        $this->sXMLExportPath   = $aConfig[ 'path.xml.export' ];

        $this->sImageImportPath = $aConfig[ 'path.image.import' ];

        $this->sBoxPrefix       = $aConfig[ 'box.prefix' ];

        $this->sTokenSeperator  = $aConfig[ 'tokenseperator'];

        $this->oJobQueueEntity  = $oJobQueueEntity;

        $this->iJobQueueId      = $oJobQueueEntity->getId();
	}



	/*
	 * Entry point to start task
	 *
	 * @return void
	 */
	public function Execute(){

		try {

			$sProcess    = $this->sProcess;
			$iJobQueueId = $this->iJobQueueId;
			$this->oBoxDb->FlagJobProcessAsStarted( $iJobQueueId, $sProcess );

            $this->CreateArchiveDirectory();
            $this->ArchiveXml();

			// Don't flag all entities as as started. This is done in a granular way for this process.

			$this->ProcessBoxes( $this->oJobQueueEntity  );

            // This is the final task so flag job as 'complete'
			$this->oJobQueueEntity->setPid( null );
            $this->oJobQueueEntity->setStatus( 'completed' );
			$this->oJobQueueDb->InsertUpdate( $this->oJobQueueEntity );
			$this->oBoxDb->FlagJobProcessAsCompleted( $iJobQueueId, $sProcess );

		} catch (Exception $oException ) {
			$this->HandleError( $oException, $this->oJobQueueEntity );
		}

	}

    /*
     * Created a directory based on the Job ID
     *
     * @return void
     */
    private function CreateArchiveDirectory(){

        $sDirectory            = $this->sArchivePath . DIRECTORY_SEPARATOR . $this->iJobQueueId;

        $this->sJobArchivePath = $sDirectory;

        if( !is_dir( $sDirectory ) ) {

        	$this->oLogger->Log( 'Creating directory ' . $sDirectory );

            if( !mkdir( $sDirectory, 0775, true )) {
            	throw new ImporterException( 'Failed to create ' . $sDirectory );
            }

            $this->oLogger->Log( 'Created directory ' . $sDirectory );
        }
    }

    /*
     * Move XML
     *
     * @return void
     */
    private function ArchiveXml(){

        $sSourceXmlPath = $this->ConstructSourceXmlPath();
        $sTargetXmlPath = $this->ConstructTargetXmlPath();

        if( !rename( $sSourceXmlPath, $sTargetXmlPath ) ) {
        	throw new ImporterException( 'ArchiveXml(): unable to rename ' . $sSourceXmlPath . ' to ' . $sTargetXmlPath );
        }

        $this->oLogger->Log( $sSourceXmlPath . ' moved to ' . $sTargetXmlPath );
    }

    /*
     * @return string
     */
    private function ConstructSourceXmlPath(){
        return $this->sXMLExportPath . DIRECTORY_SEPARATOR . $this->iJobQueueId . '.xml';
    }

    /*
     *
     */
    private function ConstructTargetXmlPath(){
        return $this->sJobArchivePath . DIRECTORY_SEPARATOR . $this->iJobQueueId . '.xml';
    }

    /*
     * @return string
     */
	protected function ConstructPath(
									  BoxEntity   $oBoxEntity
									, FolioEntity $oFolioEntity
									, ItemEntity  $oItemEntity ){

        $sBoxNumber       = $oBoxEntity->getBoxNumber();

        $this->sBoxNumber = $sBoxNumber;

        $sFolioNumber     = $oFolioEntity->getFolioNumber();

        $sItemNumber      = $oItemEntity->getItemNumber();

        $sTokenSeperator = $this->sTokenSeperator;

        $sImagePath       = $sBoxNumber . $sTokenSeperator . $sFolioNumber  . $sTokenSeperator . $sItemNumber;

        return $sImagePath;

	}

	/*
	 * Move all item images to the archive directory
	 *
	 * @return void
	 */
	protected function Process( $sImagePath ){

        $sRootPath             = $this->sImageImportPath;

        $sBoxPrefix            = $this->sBoxPrefix;

        $sBoxNumber            = $this->sBoxNumber;

        $sSourceImageDirectory = $sRootPath . DIRECTORY_SEPARATOR . $sBoxPrefix . $sBoxNumber;

        $sSourceImagePath      = $sSourceImageDirectory . DIRECTORY_SEPARATOR . $sImagePath . '.jpg';

        if( file_exists( $sSourceImagePath )){

	        $sTargetImageDirectory = $this->sJobArchivePath . DIRECTORY_SEPARATOR . $sBoxPrefix . $sBoxNumber;

	        if( !is_dir( $sTargetImageDirectory ) ){

	        	if( !mkdir( $sTargetImageDirectory, 0775, true )) {
			    	throw new ImporterException( 'Failed to create ' . $sTargetImageDirectory );
				}

	            $this->oLogger->Log( 'Created directory ' . $sTargetImageDirectory );
	        }

	        $sTargetImagePath      = $sTargetImageDirectory . DIRECTORY_SEPARATOR . $sImagePath . '.jpg';

	        if( !rename( $sSourceImagePath, $sTargetImagePath ) ) {
	        	throw new ImporterException( 'Process(): unable to rename ' . $sSourceImagePath . ' to ' . $sTargetImagePath );
	        }

	        $this->oLogger->Log( $sSourceImagePath . ' moved to ' . $sTargetImagePath );

        }else{
        	$this->oLogger->Log( $sSourceImagePath . ' no longer exists. Probably previously archived.' );
        }


        if( file_exists( $sSourceImageDirectory )){

	        //Delete source 'box_' directory if empty

	        $aFiles = scandir( $sSourceImageDirectory );

	        if( !$aFiles ) {
	        	throw new ImporterException( 'Process(): unable to scan source image directory: ' . $sSourceImageDirectory );
	        }

	        // If the parent directory is now empty then delete

	        if( count( $aFiles) < 3 ){

	        	$this->oLogger->Log( $sSourceImageDirectory . ' is empty. Deleting ...');
	            $this->oFile->DeleteDirectory( $sSourceImageDirectory );
	            $this->oLogger->Log( 'Deleted ' . $sSourceImageDirectory );

	        }
        }

	}
}
































