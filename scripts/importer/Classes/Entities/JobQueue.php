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

class JobQueue extends EntityAbstract{

	// Ordinarily these would be private but need to be public for the installer Zend paginator

	public $user_id;
	public $job_status;
	public $job_start_time;
	public $job_end_time;
	public $created;
    public $pid;


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
		$this->job_status = $status;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->job_status;
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
	public function getJobStartTime() {
		return $this->job_start_time;
	}

	/**
	 * @param string $dStarted
	 */
	public function setJobStartTime( $dStarted ) {
		$this->job_start_time = $dStarted;
	}

	/**
	 * @return string
	 */
	public function getJobEndTime() {
		return $this->job_end_time;
	}

	/**
	 * @param string $dEnded
	 */
	public function setJobEndTime( $dEnded ) {
		$this->job_end_time = $dEnded;
	}

    /**
   	 * @return string
   	 */
   	public function getPid() {
   		return $this->pid;
   	}

   	/**
   	 * @param string $iPid
   	 */
   	public function setPid( $iPid ) {
   		$this->pid = $iPid;
   	}

	/*
	 *
	*/
	public function SetAllPropertiesToNull(){
		foreach( $this as $property => $value) {
			$this->{$property} = NULL;
		}
	}

	/*
	 * Required for the for the installer Zend paginator
	 *
	 * @return void
	*/
	public function exchangeArray( $data ){
		parent::exchangeArray( $data );
		$this->user_id        = ( !empty( $data[ 'user_id' ] ) ) ? $data[ 'user_id' ] : null;
		$this->job_status     = ( !empty( $data[ 'job_status' ] ) ) ? $data[ 'job_status' ] : null;
		$this->job_start_time = ( !empty( $data[ 'job_start_time' ] ) ) ? $data[ 'job_start_time']  : null;
		$this->job_end_time   = ( !empty( $data[ 'job_end_time' ] ) ) ? $data[ 'job_end_time']  : null;
		$this->created        = ( !empty( $data[ 'created' ] ) ) ? $data[ 'created']  : null;
		$this->pid            = ( !empty( $data[ 'pid' ] ) ) ? $data[ 'pid']  : null;
	}

}



































