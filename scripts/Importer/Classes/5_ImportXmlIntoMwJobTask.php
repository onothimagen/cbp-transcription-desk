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

use Classes\Helpers\File as FileHelper;


class ImportXmlIntoMwJobTask extends TaskAbstract{

	private $sXMLExportPath;

	private $sMwImporterPath;

	private $iJobQueueId;

	public function __construct(  $oDi
								, $aSectionConfig
								, $iJobQueueId ){

		parent::__construct( $oDi );

		$this->sXMLExportPath   = $aSectionConfig[ 'path.xml.export' ];

		$this->sMwImporterPath	= $aSectionConfig[ 'path.mw.importer' ];

		$this->iJobQueueId      = $iJobQueueId;

		$this->sProcess        = 'import_mw';

	}



	/*
	 *
	 */
	public function Execute(){

		//$this->FlagProcessAsStarted();


		// Needs to be compound

		//$this->UpdateJobProcessStatus();

		//$this->FlagProcessAsCompleted();

	}



	/*
	 *
	 */
	private function ImportXmlIntoMw(){

		$sXmlFileName = $this->sXMLExportPath . DIRECTORY_SEPARATOR . $this->iJobQueueId . '.xml';

		$sPhpPath = 'php';

		if( $this->oFile->ServerOS() == 'WIN' ){
			$sPhpPath = 'php-cgi';
		}

		$sCommand       = $sPhpPath . ' ' . $this->sMwImporterPath . ' < ' . $sXmlFileName;

		ob_start();
		passthru( $sCommand );
		$sPhpOutput = ob_get_contents();
		ob_end_clean();

		echo $sPhpOutput;


	}


}
































