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

namespace Classes\Entities;


class Folio extends EntityAbstract{

	/* Ordinarily these would be private but need to be public for the installer Zend paginator */

	public $id_number;
	public $box_id;
	public $folio_number;
	public $second_folio_number;
	public $category;
	public $recto_verso;
	public $creator;
	public $recipient;
	public $penner;
	public $marginals;
	public $corrections;
	public $date_1;
	public $date_2;
	public $date_3;
	public $date_4;
	public $date_5;
	public $date_6;
	public $estimated_date;
	public $info_in_main_heading_field;
	public $main_heading;
	public $sub_headings;
	public $marginal_summary_numbering;
	public $number_of_pages;
	public $page_numbering;
	public $titles;
	public $watermarks;
	public $paper_producer;
	public $paper_producer_in_year;
	public $notes_public;
	public $box_number;
	public $item_number;

	/**
	 * @param int $id_number
	 */
	public function setIdNumber( $id_number ) {
		$this->id_number = $id_number;
		return $id_number;
	}

	/**
	 * @return int
	 */
	public function getIdNumber() {
		return $this->id_number;
	}


	/**
	 * @param int $box_id
	 */
	public function setBoxId( $box_id ) {
		$this->box_id = $box_id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getBoxId() {
		return $this->box_id;
	}

	/**
	 * @param string $folio_number
	 */
	public function setFolioNumber( $folio_number ) {
		$this->folio_number = $folio_number;
	}

	/**
	 * @return string
	 */
	public function getFolioNumber() {
		return $this->folio_number;
	}

	/**
	 * @param string $second_folio_number
	 */
	public function setSecondFolioNumber( $second_folio_number ) {
		$this->second_folio_number = $second_folio_number;
	}

	/**
	 * @return string
	 */
	public function getSecondFolioNumber() {
		return $this->second_folio_number;
	}


	/**
	 * @param string $category
	 */
	public function setCategory( $category ) {
		$this->category = $category;
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @param string $recto_verso
	 */
	public function setRectoVerso( $recto_verso ) {
		$this->recto_verso = $recto_verso;
	}

	/**
	 * @return string
	 */
	public function getRectoVerso() {
		return $this->recto_verso;
	}


	/**
	 * @param string $creator
	 */
	public function setcreator( $creator ) {
		$this->creator = $creator;
	}

	/**
	 * @return string
	 */
	public function getCreator() {
		return $this->creator;
	}

	/**
	 * @param string $recipient
	 */
	public function setrecipient( $recipient ) {
		$this->recipient = $recipient;
	}

	/**
	 * @return string
	 */
	public function getRecipient() {
		return $this->recipient;
	}

	/**
	 * @param string $penner
	 */
	public function setPenner( $penner ) {
		$this->Penner = $penner;
	}

	/**
	 * @return string
	 */
	public function getPenner() {
		return $this->penner;
	}

	/**
	 * @param string $marginals
	 */
	public function setMarginals( $marginals ) {
		$this->marginals = $marginals;
	}

	/**
	 * @return string
	 */
	public function getMarginals() {
		return $this->marginals;
	}

	/**
	 * @param string $corrections
	 */
	public function setcorrections( $corrections ) {
		$this->corrections = $corrections;
	}

	/**
	 * @return string
	 */
	public function getCorrections() {
		return $this->corrections;
	}

	/**
	 * @param string $date_1
	 */
	public function setDate_1( $date_1 ) {
		$this->date_1 = $date_1;
	}

	/**
	 * @return string
	 */
	public function getDate_1() {
		return $this->date_1;
	}

	/**
	 * @param string $date_2
	 */
	public function setDate_2( $date_2 ) {
		$this->date_2 = $date_2;
	}

	/**
	 * @return string
	 */
	public function getDate_2() {
		return $this->date_2;
	}

	/**
	 * @param string $date_3
	 */
	public function setDate_3( $date_3 ) {
		$this->date_3 = $date_3;
	}

	/**
	 * @return string
	 */
	public function getDate_3() {
		return $this->date_3;
	}

	/**
	 * @param string $date_4
	 */
	public function setDate_4( $date_4 ) {
		$this->date_4 = $date_4;
	}

	/**
	 * @return string
	 */
	public function getDate_4() {
		return $this->date_4;
	}

	/**
	 * @param string $date_5
	 */
	public function setDate_5( $date_5 ) {
		$this->date_5 = $date_5;
	}

	/**
	 * @return string
	 */
	public function getDate_5() {
		return $this->date_5;
	}

	/**
	 * @param string $date_6
	 */
	public function setDate_6( $date_6)  {
		$this->date_6 = $date_6;
	}

	/**
	 * @return string
	 */
	public function getDate_6() {
		return $this->date_6;
	}

	/**
	 * @param string $estimated_date
	 */
	public function setEstimatedDate( $estimated_date ) {
		$this->estimated_date = $estimated_date;
	}

	/**
	 * @return string
	 */
	public function getEstimatedDate() {
		return $this->estimated_date;
	}

	/**
	 * @param string $info_in_main_heading_field
	 */
	public function setInfoInMainHeadingField( $info_in_main_heading_field ) {
		$this->info_in_main_heading_field = $info_in_main_heading_field;
	}

	/**
	 * @return string
	 */
	public function getInfoInMainHeadingField() {
		return $this->info_in_main_heading_field;
	}

	/**
	 * @param string $main_heading
	 */
	public function setMainHeading( $main_heading ) {
		$this->main_heading = $main_heading;
	}

	/**
	 * @return string
	 */
	public function getMainHeading() {
		return $this->main_heading;
	}


	/**
	 * @param string $sub_headings
	 */
	public function setSubHeadings( $sub_headings ) {
		$this->sub_headings = $sub_headings;
	}


	/**
	 * @return string
	 */
	public function getSubHeadings() {
		return $this->sub_headings;
	}

	/**
	 * @param string $marginal_summary_numbering
	 */
	public function setMarginalSummaryNumbering( $marginal_summary_numbering ) {
		$this->marginal_summary_numbering = $marginal_summary_numbering;
	}


	/**
	 * @return string
	 */
	public function getMarginalSummaryNumbering() {
		return $this->marginal_summary_numbering;
	}

	/**
	 * @param string $number_of_pages
	 */
	public function setNumberOfPages( $number_of_pages ) {
		$this->number_of_pages = $number_of_pages;
	}

	/**
	 * @return string
	 */
	public function getNumberOfPages() {
		return $this->number_of_pages;
	}

	/**
	 * @param string $page_numbering
	 */
	public function setPageNumbering( $page_numbering ) {
		$this->page_numbering = $page_numbering;
	}

	/**
	 * @return string
	 */
	public function getPageNumbering() {
		return $this->page_numbering;
	}


	/**
	 * @param string $titles
	 */
	public function setTitles( $titles ) {
		$this->titles = $titles;
	}

	/**
	 * @return string
	 */
	public function getTitles() {
		return $this->titles;
	}

	/**
	 * @param string $watermarks
	 */
	public function setWatermarks( $watermarks ) {
		$this->watermarks = $watermarks;
	}

	/**
	 * @return string
	 */
	public function getWatermarks() {
		return $this->watermarks;
	}

	/**
	 * @param string $paper_producer
	 */
	public function setPaperProducer( $paper_producer ) {
		$this->paper_producer = $paper_producer;
	}

	/**
	 * @return string
	 */
	public function getPaperProducer() {
		return $this->paper_producer;
	}

	/**
	 * @param string $paper_producer_in_year
	 */
	public function setPaperProducerInYear( $paper_producer_in_year ) {
		$this->paper_producer_in_year = $paper_producer_in_year;
	}

	/**
	 * @return string
	 */
	public function getPaperProducerInYear() {
		return $this->paper_producer_in_year;
	}

	/**
	 * @param string $notes_public
	 */
	public function setNotesPublic( $notes_public ) {
		$this->notes_public = $notes_public;
	}

	/**
	 * @return string
	 */
	public function getBoxNumber() {
		return $this->box_number;
	}

	/**
	 * @param string $box_number
	 */
	public function setBoxNumber( $box_number ) {
		$this->box_number = $box_number;
	}


	/**
	 * @return string
	 */
	public function getItemNumber() {
		return $this->item_number;
	}

	/**
	 * @param field_type $item_number
	 */
	public function setItemNumber( $item_number ) {
		$this->item_number = $item_number;
	}


	/**
	 * @return string
	 */
	public function getNotesPublic() {
		return $this->notes_public;
	}


	/*
	 * Required for the for the installer Zend paginator
	 *
	 * @return void
	*/
	public function exchangeArray( $data ){
		parent::exchangeArray( $data );
		$this->id_number                    = ( !empty( $data[ 'id_number'                  ] ) ) ? $data[ 'id_number'                  ] : null;
		$this->box_id                       = ( !empty( $data[ 'box_id'                     ] ) ) ? $data[ 'box_id'                     ] : null;
		$this->folio_number                 = ( !empty( $data[ 'folio_number'               ] ) ) ? $data[ 'folio_number'               ] : null;
		$this->second_folio_number          = ( !empty( $data[ 'second_folio_number'        ] ) ) ? $data[ 'second_folio_number'        ] : null;
		$this->category                     = ( !empty( $data[ 'category'                   ] ) ) ? $data[ 'category'                   ] : null;
		$this->recto_verso                  = ( !empty( $data[ 'recto_verso'                ] ) ) ? $data[ 'recto_verso'                ] : null;
		$this->creator                      = ( !empty( $data[ 'creator'                    ] ) ) ? $data[ 'creator'                    ] : null;
		$this->recipient                    = ( !empty( $data[ 'recipient'                  ] ) ) ? $data[ 'recipient'                  ] : null;
		$this->penner                       = ( !empty( $data[ 'penner'                     ] ) ) ? $data[ 'penner'                     ] : null;
		$this->marginals                    = ( !empty( $data[ 'marginals'                  ] ) ) ? $data[ 'marginals'                  ] : null;
		$this->corrections                  = ( !empty( $data[ 'corrections'                ] ) ) ? $data[ 'corrections'                ] : null;
		$this->date_1                       = ( !empty( $data[ 'date_1'                     ] ) ) ? $data[ 'date_1'                     ] : null;
		$this->date_2                       = ( !empty( $data[ 'date_2'                     ] ) ) ? $data[ 'date_2'                     ] : null;
		$this->date_3                       = ( !empty( $data[ 'date_3'                     ] ) ) ? $data[ 'date_3'                     ] : null;
		$this->date_4                       = ( !empty( $data[ 'date_4'                     ] ) ) ? $data[ 'date_4'                     ] : null;
		$this->date_5                       = ( !empty( $data[ 'date_5'                     ] ) ) ? $data[ 'date_5'                     ] : null;
		$this->date_6                       = ( !empty( $data[ 'date_6'                     ] ) ) ? $data[ 'date_6'                     ] : null;
		$this->estimated_date               = ( !empty( $data[ 'estimated_date'             ] ) ) ? $data[ 'estimated_date'             ] : null;
		$this->info_in_main_heading_field   = ( !empty( $data[ 'info_in_main_heading_field' ] ) ) ? $data[ 'info_in_main_heading_field' ] : null;
		$this->main_heading                 = ( !empty( $data[ 'main_heading'               ] ) ) ? $data[ 'main_heading'               ] : null;
		$this->sub_headings                 = ( !empty( $data[ 'sub_headings'               ] ) ) ? $data[ 'sub_headings'               ] : null;
		$this->marginal_summary_numbering   = ( !empty( $data[ 'marginal_summary_numbering' ] ) ) ? $data[ 'marginal_summary_numbering' ] : null;
		$this->number_of_pages              = ( !empty( $data[ 'number_of_pages'            ] ) ) ? $data[ 'number_of_pages'            ] : null;
		$this->page_numbering               = ( !empty( $data[ 'page_numbering'             ] ) ) ? $data[ 'page_numbering'             ] : null;
		$this->titles                       = ( !empty( $data[ 'titles'                     ] ) ) ? $data[ 'titles'                     ] : null;
		$this->watermarks                   = ( !empty( $data[ 'watermarks'                 ] ) ) ? $data[ 'watermarks'                 ] : null;
		$this->paper_producer               = ( !empty( $data[ 'paper_producer'             ] ) ) ? $data[ 'paper_producer'             ] : null;
		$this->paper_producer_in_year       = ( !empty( $data[ 'paper_producer_in_year'     ] ) ) ? $data[ 'paper_producer_in_year'     ] : null;
		$this->notes_public                 = ( !empty( $data[ 'notes_public'               ] ) ) ? $data[ 'notes_public'               ] : null;
		$this->box_number                   = ( !empty( $data[ 'box_number'                 ] ) ) ? $data[ 'box_number'                 ] : null;
		$this->item_number                  = ( !empty( $data[ 'item_number'                ] ) ) ? $data[ 'item_number'                ] : null;
	}


}








































