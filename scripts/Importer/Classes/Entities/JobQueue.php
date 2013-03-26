<?php

namespace Classes\Entities;

class JobQueue{


	private $iId       = NULL;
	private $iUserId   = NULL;
	private $sStatus   = NULL;
	private $sCreated  = NULL;
	private $dStarted  = NULL;
	private $dEnded    = NULL;



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
	 * @param string $sUserId
	 */
	public function setUserId( $iUserId ) {
		$this->iUserId = $iUserId;
	}

	/**
	 * @return string
	 */
	public function getUserId() {
		return $this->iUserId;
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
	 * @param string $sCreated
	 */
	public function setCreated( $sCreated ) {
		$this->sCreated = $sCreated;
	}

	/**
	 * @return string
	 */
	public function getCreated() {
		return $this->sCreated;
	}


	/**
	 * @return string
	 */
	public function getStarted() {
		return $this->dStarted;
	}

	/**
	 * @param string $dstarted
	 */
	public function setStarted( $dStarted ) {
		$this->dStarted = $dStarted;
	}


	/**
	 * @return string
	 */
	public function getEnded() {
		return $this->dEnded;
	}

	/**
	 * @param string $dended
	 */
	public function setEnded( $dEnded ) {
		$this->dEnded = $dEnded;
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
