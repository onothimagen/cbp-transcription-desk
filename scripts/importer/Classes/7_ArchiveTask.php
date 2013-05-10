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

        $this->oJobQueueEntity  = $oJobQueueEntity;

        $this->iJobQueueId      = $oJobQueueEntity->getId();
	}



	/*
	 *
	 */
	public function Execute(){

		try {

            $this->CreateArchiveDirectory();
            $this->ArchiveXml();

			// Don't flag all entities as as started. This is done in a granular way for this process.

			$this->ProcessBoxes();

            // This is the final task so flag job as 'complete'
            $this->oJobQueueEntity->setStatus( 'completed' );
			$this->oJobQueueDb->InsertUpdate( $this->oJobQueueEntity );

		} catch (Exception $oException ) {
			$this->HandleError( $oException, $this->oJobQueueEntity );
		}

	}

    /*
     *
     */
    private function CreateArchiveDirectory(){

        $sDirectory            = $this->sArchivePath . DIRECTORY_SEPARATOR . $this->iJobQueueId;

        $this->sJobArchivePath = $sDirectory;

        $this->oLogger->Log( 'Creating directory ' . $sDirectory );

        if( file_exists( $sDirectory ) === false ){

            mkdir( $sDirectory );
            $this->oLogger->Log( 'Created directory ' . $sDirectory );
        }
    }

    private function ArchiveXml(){

        $sSourceXmlPath = $this->ConstructSourceXmlPath();
        $sTargetXmlPath = $this->ConstructTargetXmlPath();

        rename( $sSourceXmlPath, $sTargetXmlPath );

        $this->oLogger->Log( $sSourceXmlPath . ' moved to ' . $sTargetXmlPath );
    }

    /*
     *
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
     *
     */
	protected function ConstructPath(
									  BoxEntity   $oBoxEntity
									, FolioEntity $oFolioEntity
									, ItemEntity  $oItemEntity ){

        $sBoxNumber       = $oBoxEntity->getBoxNumber();

        $this->sBoxNumber = $sBoxNumber;

        $sFolioNumber     = $oFolioEntity->getFolioNumber();

        $sItemNumber      = $oItemEntity->getItemNumber();

        $sImagePath       = $sBoxNumber . '_' . $sFolioNumber  . '_' . $sItemNumber;

        return $sImagePath;

	}

	/*
	 *
	 */
	protected function Process( $sImagePath ){

        $sRootPath             = $this->sImageImportPath;

        $sBoxPrefix            = $this->sBoxPrefix;

        $sBoxNumber            = $this->sBoxNumber;

        $sSourceImageDirectory = $sRootPath . DIRECTORY_SEPARATOR . $sBoxPrefix . $sBoxNumber;

        $sSourceImagePath      = $sSourceImageDirectory . DIRECTORY_SEPARATOR . $sImagePath . '.jpg';

        $sTargetImageDirectory = $this->sJobArchivePath . DIRECTORY_SEPARATOR . $sBoxPrefix . $sBoxNumber;

        if( file_exists( $sTargetImageDirectory ) === false ){
            mkdir( $sTargetImageDirectory );
            $this->oLogger->Log( 'Created directory ' . $sTargetImageDirectory );
        }

        $sTargetImagePath      = $sTargetImageDirectory . DIRECTORY_SEPARATOR . $sImagePath . '.jpg';

        rename( $sSourceImagePath, $sTargetImagePath );

        $this->oLogger->Log( $sSourceImagePath . ' moved to ' . $sTargetImagePath );

        //Delete source 'box_' directory if empty

        $aFiles = scandir( $sSourceImageDirectory );

        if( count( $aFiles) < 3 ){
            $this->oFile->DeleteDirectory( $sSourceImageDirectory );
            $this->oLogger->Log( 'Deleted ' . $sSourceImageDirectory );
        }

	}
}
































