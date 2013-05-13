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
use Classes\Db\MediaWiki as MediaWikiDb;


use Classes\Entities\JobQueue as JobQueueEntity;
use Classes\Entities\Box      as BoxEntity;
use Classes\Entities\Folio    as FolioEntity;
use Classes\Entities\Item     as ItemEntity;

use Classes\Exceptions\Importer as ImporterException;

class VerifyPagesTask  extends TaskAbstract{

	/* @var FolioEntity */
	private $oFolioItemEntity;

	/* @var MediaWikiDb */
	private $oMediaWikiDb;

	private $sPagePrefix;

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

        $this->oLogger->SetContext( 'jobs', $this->iJobQueueId );

	}



	/*
	 *
	 */
	public function Execute(){

		try {
			$this->ProcessBoxes();

		} catch (Exception $oException ) {
			$this->HandleError( $oException, $oJobQueueEntity );
		}

	}

	/*
	 *
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
	 *
	 */
	protected function Process( $sPageTitle ){

		$sPagePrefix = $this->sPagePrefix;

		$sPageTitle = $sPagePrefix . '/' . $sPageTitle;

		$bIsItemInMediaWiki = $this->oMediaWikiDb->DoesItemPageExist( $sPageTitle );

		if( $bIsItemInMediaWiki === false){
			throw new ImporterException( $sPageTitle . ' was not found in MediaWiki' );
		}

	}

	/*
	 *
	*/
	protected function PseudoSetterForVerifyAutoComplete( MediaWikiDb $oMediaWikiDb ){

		$this->oMediaWikiDb  = $oMediaWikiDb;

	}


}































