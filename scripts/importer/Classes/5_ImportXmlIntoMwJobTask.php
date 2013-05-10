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

use Classes\Db\Box            as BoxDb;

use Classes\Helpers\File      as FileHelper;
use Classes\Helpers\Logger;

use Classes\Entities\JobQueue   as JobQueueEntity;

use Classes\Exceptions\Importer as ImporterException;

class ImportXmlIntoMwJobTask extends TaskAbstract{

	private $sXMLExportPath;

	private $sMwImporterPath;


	public function __construct(  Di             $oDi
								,                $aConfig
								, JobQueueEntity $oJobQueueEntity ){

		parent::__construct( $oDi );

		$this->sXMLExportPath   = $aConfig[ 'path.xml.export' ];

		$this->sMwImporterPath  = $aConfig[ 'path.mw.importer' ];


        $this->sProcess         = 'import_mw';

        $this->sPreviousProcess = 'export';

        $this->oJobQueueEntity  = $oJobQueueEntity;
        $this->iJobQueueId      = $oJobQueueEntity->getId();

        $this->oLogger->SetContext( 'jobs', $this->iJobQueueId );

	}



	/*
	 *
	 */
	public function Execute(){

		try {
			$sProcess = $this->sProcess;
			$iJobQueueId = $this->iJobQueueId;
			$this->oBoxDb->FlagJobProcessAsStarted( $iJobQueueId, $sProcess );
			$this->CheckPaths();
			$this->ImportXmlIntoMw();
			$this->oBoxDb->FlagJobProcessAsCompleted( $iJobQueueId, $sProcess );
		} catch ( ImporterException $oException ) {
			$this->HandleError( $oException, $this->oJobQueueEntity );
		}
	}


	/*
	 *
	*/
	private function CheckPaths(){

		$this->oFile->CheckExists( 'XMLExportPath', $this->sXMLExportPath );
		$this->oFile->CheckExists( 'MwImporterPath', $this->sMwImporterPath );

	}



	/*
	 *
	 */
	private function ImportXmlIntoMw(){

		$sXmlFileName = $this->sXMLExportPath . DIRECTORY_SEPARATOR . $this->iJobQueueId . '.xml';

		$this->oFile->CheckExists( 'XmlFileName', $sXmlFileName );

		$sCommand     = 'php ' . $this->sMwImporterPath . ' < ' . $sXmlFileName;

		$this->oLogger->Log( 'Executing command: ' . $sCommand );

		$sPhpOutput = '';

		ob_start();
		system( $sCommand );
		$sPhpOutput = ob_get_contents();
		ob_end_clean();

		if( strpos( $sPhpOutput, 'Done!' ) !== false ){
	        $this->oLogger->Log( 'Import into MW successful' );
            return true;
        }

        $this->oLogger->Log( $sPhpOutput );

        if(strpos( $sPhpOutput, 'Done!' ) === false ){
			throw new ImporterException( 'Done! was not returned by MW\'s importDump.php utility. Output returned: ' . $sPhpOutput );
		}


	}


}
































