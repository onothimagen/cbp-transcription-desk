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

namespace Classes\Db;

use Zend\Db\Adapter\Driver\Pdo\Result;
use Zend\Db\Adapter\Adapter;

class MediaWiki extends DbAbstract{


	/*
	 *
	*/
	public function __construct( Adapter $oAdapter ){
		parent::__construct( $oAdapter );
		$this->sTableName = 'page';
	}


	/*
	 * @return boolean
	 */
	public function DoesItemPageExist( $sPageTitle ){

		$sPageTitleMeta = 'Meta:' . $sPageTitle;

		$sSql = 'SELECT
					page_id
				FROM
					' . $this->sTableName . '
				WHERE
					page_title = ?
				OR
					page_title = ?';

		$aBindArray = array( $sPageTitle, $sPageTitleMeta );

		$rResult = $this->Execute( $sSql, $aBindArray );

		/* There should be a basic and meta page */

		if( $rResult->count() === 2 ){
			return true;
		}

		return false;

	}

}

































