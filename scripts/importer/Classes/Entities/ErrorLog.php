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

class ErrorLog extends EntityAbstract{

	/* Ordinarily these would be private but need to be public for the installer Zend paginator */

	public $job_queue_id;
	public $box_id;
	public $folio_id;
	public $item_id;
	public $process;
	public $error;


	/**
	 * @param string $sJobQueueId
	 */
	public function setJobQueueId( $job_queue_id ) {
		$this->job_queue_id = $job_queue_id;
	}

	/**
	 * @return integer
	 */
	public function getJobQueueId() {
		return $this->job_queue_id;
	}

	/**
	 * @param string $box_id
	 */
	public function setBoxId( $box_id ) {
		$this->box_id = $box_id;
	}

	/**
	 * @return string
	 */
	public function getBoxId() {
		return $this->box_id;
	}


	/**
	 * @param string $sFolio
	 */
	public function setFolioId( $folio_id ) {
		$this->folio_id = $folio_id;
	}

	/**
	 * @return the $sFolio
	 */
	public function getFolioId() {
		return $this->folio_id;
	}

	/**
	 * @param string $sItem
	 */
	public function setItemId( $item_id ) {
		$this->item_id = $item_id;
	}


	/**
	 * @return string
	 */
	public function getItemId() {
		return $this->item_id;
	}

	/**
	 * @param string $sDescription
	 */
	public function setError( $error ) {
		$this->error = $error;
	}

	/**
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}


	/**
	 * @return string
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param string $dCreated
	 */
	public function setCreated( $created ) {
		$this->created = $created;
	}



	/*
	 *
	*/
	public function SetAllPropertiesToNull(){
		foreach( $this as $property => $value) {
			$this->{$property} = null;
		}
	}

	/*
	 * Required for the for the installer Zend paginator
	 *
	 * @return void
	*/
	public function exchangeArray( $data ){
		parent::exchangeArray( $data );
		$this->job_queue_id = ( !empty( $data[ 'job_queue_id' ] ) ) ? $data[ 'job_queue_id' ] : null;
		$this->box_id       = ( !empty( $data[ 'box_id' ] ) ) ? $data[ 'box_id' ] : null;
		$this->folio_id     = ( !empty( $data[ 'folio_id' ] ) ) ? $data[ 'folio_id' ] : null;
		$this->item_id      = ( !empty( $data[ 'item_id' ] ) ) ? $data[ 'item_id' ] : null;
		$this->error        = ( !empty( $data[ 'error' ] ) ) ? $data[ 'error' ] : null;
	}


}








































