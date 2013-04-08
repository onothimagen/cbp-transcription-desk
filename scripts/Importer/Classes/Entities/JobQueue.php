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

class JobQueue{


	private $id             = NULL;
	private $user_d         = NULL;
	private $status         = NULL;
	private $job_start_time = NULL;
	private $job_end_time   = NULL;
	private $created        = NULL;



	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $iId;
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @param string $sUserId
	 */
	public function setUserId( $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * @return string
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * @param string $sProcessStatus
	 */
	public function setStatus( $status ) {
		$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $sCreated
	 */
	public function setCreated( $created ) {
		$this->created = $created;
	}

	/**
	 * @return string
	 */
	public function getCreated() {
		return $this->created;
	}


	/**
	 * @return string
	 */
	public function getStarted() {
		return $this->job_start_time;
	}

	/**
	 * @param string $dstarted
	 */
	public function setStarted( $dStarted ) {
		$this->job_start_time = $dStarted;
	}


	/**
	 * @return string
	 */
	public function getEnded() {
		return $this->job_end_time;
	}

	/**
	 * @param string $dended
	 */
	public function setEnded( $dEnded ) {
		$this->job_end_time = $dEnded;
	}


	/*
	 *
	*/
	public function SetAllPropertiesToNull(){
		foreach( $this as $property => $value) {
			$this->{$property} = NULL;
		}
	}

}
