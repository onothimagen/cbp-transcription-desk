<?php

namespace Classes\Entities;

abstract class EntityAbstract{

	protected $id;
	protected $process;
	protected $process_status;
	protected $process_start_time;
	protected $process_end_time;
	protected $updated;
	protected $created;

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
	 * @param string $process
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
	 * @param string $status
	 */
	public function setProcessStatus( $process_status ) {
		$this->process_status = $process_status;
	}

	/**
	 * @return string
	 */
	public function getProcessStatus() {
		return $this->process_status;
	}


	/**
	 * @param field_type $process_start_time
	 */
	public function setProcessStartTime( $process_start_time ) {
		$this->process_start_time = $process_start_time;
	}

	/**
	 * @return string
	 */
	public function getProcessEndTime() {
		return $this-> 	process_end_time;
	}

	/**
	 * @param field_type $process_end_time
	 */
	public function setProcessEndTime( $process_end_time ) {
		$this->process_end_time = $process_end_time;
	}


	/**
	 * @return string
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param string $updated
	 */
	public function setUpdated( $updated ) {
		$this->updated = $updated;
	}


	/**
	 * @return string
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param string $created
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
