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

namespace Classes\Entities;

class ErrorLog{


	private $id;
	private $job_queue_id;
	private $box_id;
	private $folio_id;
	private $item_id;
	private $process;
	private $error;
	private $created;


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id;
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

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
	 * @param string $sProcess
	 */
	public function setProcess( $process ) {
		$this->process = $process;
	}

	/**
	 * @return string
	 */
	public function getProcess() {
		return $this->process;
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

}
