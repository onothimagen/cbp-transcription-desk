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
use Classes\Db\MediaWiki as MediaWikiDb;


use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Box      as BoxEntity;
use Classes\Entities\Folio    as FolioEntity;
use Classes\Entities\Item     as ItemEntity;

use Classes\Exceptions\Importer as ImporterException;

/*
 * This class checks that the pages imported in the previous process
 * exist in mediawiki as pages
 */
class VerifyPagesTask  extends TaskAbstract{

	/* @var FolioEntity */
	private $oFolioItemEntity;

	/* @var MediaWikiDb */
	private $oMediaWikiDb;

	private $sPagePrefix;



   /*
	* @param Di            $oDi
	* @param string[]       $aConfig
	* @param JobQueueEntity $oJobQueueEntity
	* @return void
	*/
	public function __construct(  Di             $oDi
								,                $aConfig
								, JobQueueEntity $oJobQueueEntity ){

		parent::__construct( $oDi );

        $this->oJobQueueEntity = $oJobQueueEntity;

		/* @var MediaWikiDb */
		$this->oMediaWikiDb     = $oDi->get( 'Classes\Db\MediaWiki' );

		$this->sProcess         = 'verify';

		$this->sPreviousProcess = 'import_mw';

		$this->sPagePrefix      = $aConfig[ 'page.prefix' ];

		$this->iJobQueueId      = $oJobQueueEntity->getId();


	}



	/*
	 * Entry point to start task
	 *
	 * @return void
	 */
	public function Execute(){

		try {
	        $this->oLogger->ConfigureLogger( 'jobs', $this->iJobQueueId );

			$sProcess    = $this->sProcess;
			$iJobQueueId = $this->iJobQueueId;

			$this->oBoxDb->FlagJobProcessAsStarted( $iJobQueueId, $sProcess );

			$this->ProcessBoxes( $this->oJobQueueEntity  );

			$this->oBoxDb->FlagJobProcessAsCompleted( $iJobQueueId, $sProcess );

		} catch (Exception $oException ) {
			$this->HandleError( $oException, $oJobQueueEntity );
		}

	}

	/*
	 * The page's title in mediawiki to search for
	 *
	 * @return string
	 */
	protected function ConstructPath(
									  BoxEntity   $oBoxEntity
									, FolioEntity $oFolioEntity
									, ItemEntity  $oItemEntity ){

		$sBoxNumber   = $oBoxEntity->getBoxNumber();

		$sFolioNumber = $oFolioEntity->getFolioNumber();

		$sItemNumber  = $oItemEntity->getItemNumber();

		$sItemPath  = $sBoxNumber . '/' . $sFolioNumber  . '/' . $sItemNumber;

		return $sItemPath;

	}

	/*
	 * Search for the page's title in mediawiki
	 *
	 * @return void
	 */
	protected function Process( $sPageTitle ){

		$sPagePrefix = $this->sPagePrefix;

		$sPageTitle = $sPagePrefix . '/' . $sPageTitle;

		$this->oLogger->Log ( 'Checking whether ' . $sPageTitle . ' exists in mediawiki'  );

		$bIsItemInMediaWiki = $this->oMediaWikiDb->DoesItemPageExist( $sPageTitle );

		if( $bIsItemInMediaWiki === false){
			$this->oLogger->Log ( $sPageTitle . ' could not be found in mediawiki. Exiting Job....'  );
			throw new ImporterException( $sPageTitle . ' was not found in MediaWiki' );
		}

		$this->oLogger->Log ( $sPageTitle . ' found in mediawiki'  );

	}

	/*
	 * Stub method for problems with IDE autocomplete. Can be deleted.
	 *
	 * @return void
	*/
	protected function PseudoSetterForVerifyAutoComplete( MediaWikiDb $oMediaWikiDb ){

		$this->oMediaWikiDb  = $oMediaWikiDb;

	}


}
































