<?php

namespace Classes\Entities;

class Item{


	private $id;
	private $metadata_id;
	private $item_number;
	private $process;
	private $status;
	private $updated;
	private $completed;
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
	 * @param string $metadata_id
	 */
	public function setMetaDataId( $metadata_id ) {
		$this->metadata_id = $metadata_id;
	}

	/**
	 * @return string
	 */
	public function getMetaDataId() {
		return $this->metadata_id;
	}

	/**
	 * @param string $item_number
	 */
	public function setItemNumber( $item_number ) {
		$this->item_number = $item_number;
	}

	/**
	 * @return string
	 */
	public function getItemNumber() {
		return $this->item_number;
	}

	/**
	 * @param string $Process
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
	public function getCompleted() {
		return $this->completed;
	}

	/**
	 * @param field_type $completed
	 */
	public function setCompleted( $completed ) {
		$this->completed = $completed;
	}

	/**
	 * @return string
	 */
	public function getcreated() {
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
