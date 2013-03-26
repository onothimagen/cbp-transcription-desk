<?php

namespace Classes\Entities;

class Item{


	private $iId;
	private $iJobQueueId;
	private $iMetadataId;
	private $sItemNumber;
	private $sProcess;
	private $sStatus;
	private $dUpdated;
	private $dCompleted;
	private $dCreated;


	/**
	 * @return int
	 */
	public function getId() {
		return $this->iId;
	}

	/**
	 * @param int $iId;
	 */
	public function setId( $iId ) {
		$this->iId = $iId;
	}

	/**
	 * @param string $sJobQueueId
	 */
	public function setJobQueueId( $iJobQueueId ) {
		$this->iJobQueueId = $iJobQueueId;
	}

	/**
	 * @return string
	 */
	public function getJobQueueId() {
		return $this->iJobQueueId;
	}


	/**
	 * @param string $sMetaDataId
	 */
	public function setMetaDataId( $sMetaDataId ) {
		$this->sMetaDataId = $sMetaDataId;
	}

	/**
	 * @return string
	 */
	public function getMetaDataId() {
		return $this->sMetaDataId;
	}

	/**
	 * @param string $sItemNumber
	 */
	public function setItemNumber( $sItemNumber ) {
		$this->sItemNumber = $sItemNumber;
	}

	/**
	 * @return string
	 */
	public function getItemNumber() {
		return $this->sItemNumber;
	}

	/**
	 * @param string $sProcess
	 */
	public function setProcess( $sProcess ) {
		$this->sProcess = $sProcess;
	}

	/**
	 * @return string
	 */
	public function getProcess() {
		return $this->sProcess;
	}

	/**
	 * @param string $sStatus
	 */
	public function setStatus( $sStatus ) {
		$this->sStatus = $sStatus;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->sStatus;
	}

	/**
	 * @return string
	 */
	public function getUpdated() {
		return $this->dUpdated;
	}

	/**
	 * @param string $dUpdated
	 */
	public function setUpdated( $dUpdated ) {
		$this->dUpdated = $dUpdated;
	}

	/**
	 * @return string
	 */
	public function getCompleted() {
		return $this->dCompleted;
	}

	/**
	 * @param field_type $dCompleted
	 */
	public function setCompleted( $dCompleted ) {
		$this->dCompleted = $dCompleted;
	}

	/**
	 * @return string
	 */
	public function getCreated() {
		return $this->dCreated;
	}

	/**
	 * @param string $dCreated
	 */
	public function setCreated( $dCreated ) {
		$this->dCreated = $dCreated;
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
