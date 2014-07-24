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

namespace Classes\Mappers;

use Classes\Entities\Folio;

class CsvRowToFolioEntity{

	/* @var Folio */
	private $oFolioEntity;


	public function __construct( ){

		$this->oFolioEntity = new Folio();

	}

	/*
	 * @param string[] $aRow
	 * @return Folio
	*/
	public function Map( $aRow ){

		/* @var Folio */
		$FolioEntity = $this->oFolioEntity;

		/* Reset the FolioEntity */
		$FolioEntity->SetAllPropertiesToNull();

		$FolioEntity->setIdNumber                  ( (int) $aRow[ 'ID number' ] );
		$FolioEntity->setFolioNumber               ( $aRow[ 'Folio number' ] );
		$FolioEntity->setSecondFolioNumber         ( $aRow[ 'Second folio number' ] );
		$FolioEntity->setCategory                  ( $aRow[ 'Category' ] );
		$FolioEntity->setRectoVerso                ( $aRow[ 'Recto/Verso' ] );
		$FolioEntity->setCreator                   ( $aRow[ 'Creator' ] );
		$FolioEntity->setRecipient                 ( $aRow[ 'Recipient' ] );
		$FolioEntity->setPenner                    ( $aRow[ 'Penner' ] );
		$FolioEntity->setMarginals                 ( $aRow[ 'Marginals' ] );
		$FolioEntity->setCorrections               ( $aRow[ 'Corrections' ] );
		$FolioEntity->setDate_1                    ( $aRow[ 'Date 1' ] );
		$FolioEntity->setDate_2                    ( $aRow[ 'Date 2' ] );
		$FolioEntity->setDate_3                    ( $aRow[ 'Date 3' ] );
		$FolioEntity->setDate_4                    ( $aRow[ 'Date 4' ] );
		$FolioEntity->setDate_5                    ( $aRow[ 'Date 5' ] );
		$FolioEntity->setDate_6                    ( $aRow[ 'Date 6' ] );
		$FolioEntity->setEstimatedDate             ( $aRow[ 'Estimated date' ] );
		$FolioEntity->setInfoInMainHeadingField    ( $aRow[ 'Info in main headings field' ] );
		$FolioEntity->setMainHeading               ( $aRow[ 'Main headings' ] );
		$FolioEntity->setSubHeadings               ( $aRow[ 'Sub-headings' ] );
		$FolioEntity->setMarginalSummaryNumbering  ( $aRow[ 'Marginal summary numbering' ] );
		$FolioEntity->setNumberOfPages             ( $aRow[ 'Number of pages' ] );
		$FolioEntity->setPageNumbering             ( $aRow[ 'Page numbering' ] );
		$FolioEntity->setTitles                    ( $aRow[ 'Titles' ] );
		$FolioEntity->setWatermarks                ( $aRow[ 'Watermarks' ] );
		$FolioEntity->setPaperProducer             ( $aRow[ 'Paper producer' ] );
		$FolioEntity->setPaperProducerInYear       ( $aRow[ 'Paper produced in year' ] );
		$FolioEntity->setNotesPublic               ( $aRow[ 'Notes (public)' ] );


		return $FolioEntity;
	}



}






























