<?php

namespace Classes\Entities;


class MetaData{

	private $iId;
	private $sBoxNumber;
	private $sFolioNumber;
	private $sSecondFolioNumber;
	private $sCategory;
	private $sRectoVerso;
	private $sCreator;
	private $sRecipient;
	private $sPenner;
	private $sMarginals;
	private $sCorrections;
	private $sDate_1;
	private $sDate_2;
	private $sDate_3;
	private $sDate_4;
	private $sDate_5;
	private $sDate_6;
	private $sEstimatedDate;
	private $sInfoInMainHeadingField;
	private $sMainHeading;
	private $sSubHeadings;
	private $sMarginalSummaryNumbering;
	private $iNumberOfPages;
	private $sPageNumbering;
	private $sTitles;
	private $sWatermarks;
	private $sPaperProducer;
	private $sPaperProducerInYear;
	private $sNotesPublic;
	private $iJobQueueId;
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
	 * @param int $sBoxNumber
	 */
	public function setId( $iId ) {
		$this->iId = $iId;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBoxNumber() {
		return $this->sBoxNumber;
	}

	/**
	 * @param string $sBoxNumber
	 */
	public function setBoxNumber( $sBoxNumber ) {
		$this->sBoxNumber = $sBoxNumber;
	}

	/**
	 * @return string
	 */
	public function getFolioNumber() {
		return $this->sFolioNumber;
	}

	/**
	 * @param string $sFolioNumber
	 */
	public function setFolioNumber( $sFolioNumber ) {
		$this->sFolioNumber = $sFolioNumber;
	}

	/**
	 * @return string
	 */
	public function getSecondFolioNumber() {
		return $this->sSecondFolioNumber;
	}

	/**
	 * @param string $sSecondFolioNumber
	 */
	public function setSecondFolioNumber( $sSecondFolioNumber ) {
		$this->sSecondFolioNumber = $sSecondFolioNumber;
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return $this->sCategory;
	}

	/**
	 * @param string $sCategory
	 */
	public function setCategory( $sCategory ) {
		$this->sCategory = $sCategory;
	}

	/**
	 * @return string
	 */
	public function getRectoVerso() {
		return $this->sRectoVerso;
	}

	/**
	 * @param string $sRectoVerso
	 */
	public function setRectoVerso( $sRectoVerso ) {
		$this->sRectoVerso = $sRectoVerso;
	}

	/**
	 * @return string
	 */
	public function getCreator() {
		return $this->sCreator;
	}

	/**
	 * @param string $sCreator
	 */
	public function setCreator( $sCreator ) {
		$this->sCreator = $sCreator;
	}

	/**
	 * @return string
	 */
	public function getRecipient() {
		return $this->sRecipient;
	}

	/**
	 * @param string $sRecipient
	 */
	public function setRecipient( $sRecipient ) {
		$this->sRecipient = $sRecipient;
	}

	/**
	 * @return string
	 */
	public function getPenner() {
		return $this->sPenner;
	}

	/**
	 * @param string $sPenner
	 */
	public function setPenner( $sPenner ) {
		$this->sPenner = $sPenner;
	}

	/**
	 * @return string
	 */
	public function getMarginals() {
		return $this->sMarginals;
	}

	/**
	 * @param string $sMarginals
	 */
	public function setMarginals( $sMarginals ) {
		$this->sMarginals = $sMarginals;
	}

	/**
	 * @return string
	 */
	public function getCorrections() {
		return $this->sCorrections;
	}

	/**
	 * @param string $sCorrections
	 */
	public function setCorrections( $sCorrections ) {
		$this->sCorrections = $sCorrections;
	}

	/**
	 * @return string
	 */
	public function getDate_1() {
		return $this->sDate_1;
	}

	/**
	 * @param string $sDate_1
	 */
	public function setDate_1( $sDate_1 ) {
		$this->sDate_1 = $sDate_1;
	}

	/**
	 * @return string
	 */
	public function getDate_2() {
		return $this->sDate_2;
	}

	/**
	 * @param string $sDate_2
	 */
	public function setDate_2( $sDate_2 ) {
		$this->sDate_2 = $sDate_2;
	}

	/**
	 * @return string
	 */
	public function getDate_3() {
		return $this->sDate_3;
	}

	/**
	 * @param string $sDate_3
	 */
	public function setDate_3( $sDate_3 ) {
		$this->sDate_3 = $sDate_3;
	}

	/**
	 * @return string
	 */
	public function getDate_4() {
		return $this->sDate_4;
	}

	/**
	 * @param string $sDate_4
	 */
	public function setDate_4( $sDate_4 ) {
		$this->sDate_4 = $sDate_4;
	}

	/**
	 * @return string
	 */
	public function getDate_5() {
		return $this->sDate_5;
	}

	/**
	 * @param string $sDate_5
	 */
	public function setDate_5( $sDate_5 ) {
		$this->sDate_5 = $sDate_5;
	}

	/**
	 * @return string
	 */
	public function getDate_6() {
		return $this->sDate_6;
	}

	/**
	 * @param string $sDate_6
	 */
	public function setDate_6( $sDate_6)  {
		$this->sDate_6 = $sDate_6;
	}

	/**
	 * @return string
	 */
	public function getEstimatedDate() {
		return $this->sEstimatedDate;
	}

	/**
	 * @param string $sEstimatedDate
	 */
	public function setEstimatedDate( $sEstimatedDate ) {
		$this->sEstimatedDate = $sEstimatedDate;
	}

	/**
	 * @return string
	 */
	public function getInfoInMainHeadingField() {
		return $this->sInfoInMainHeadingField;
	}

	/**
	 * @param string $sInfoInMainHeadingField
	 */
	public function setInfoInMainHeadingField( $sInfoInMainHeadingField ) {
		$this->sInfoInMainHeadingField = $sInfoInMainHeadingField;
	}

	/**
	 * @return string
	 */
	public function getMainHeading() {
		return $this->sMainHeading;
	}

	/**
	 * @param string $sMainHeading
	 */
	public function setMainHeading( $sMainHeading ) {
		$this->sMainHeading = $sMainHeading;
	}

	/**
	 * @return string
	 */
	public function getSubHeadings() {
		return $this->sSubHeadings;
	}

	/**
	 * @param string $sSubHeadings
	 */
	public function setSubHeadings( $sSubHeadings ) {
		$this->sSubHeadings = $sSubHeadings;
	}

	/**
	 * @return string
	 */
	public function getMarginalSummaryNumbering() {
		return $this->sMarginalSummaryNumbering;
	}

	/**
	 * @param string $sMarginalSummaryNumbering
	 */
	public function setMarginalSummaryNumbering( $sMarginalSummaryNumbering ) {
		$this->sMarginalSummaryNumbering = $sMarginalSummaryNumbering;
	}

	/**
	 * @return string
	 */
	public function getNumberOfPages() {
		return $this->iNumberOfPages;
	}

	/**
	 * @param string $iNumberOfPages
	 */
	public function setNumberOfPages( $iNumberOfPages ) {
		$this->iNumberOfPages = $iNumberOfPages;
	}

	/**
	 * @return string
	 */
	public function getPageNumbering() {
		return $this->sPageNumbering;
	}

	/**
	 * @param string $sPageNumbering
	 */
	public function setPageNumbering( $sPageNumbering ) {
		$this->sPageNumbering = $sPageNumbering;
	}

	/**
	 * @return string
	 */
	public function getTitles() {
		return $this->sTitles;
	}

	/**
	 * @param string $sTitles
	 */
	public function setTitles( $sTitles ) {
		$this->sTitles = $sTitles;
	}

	/**
	 * @return string
	 */
	public function getWatermarks() {
		return $this->sWatermarks;
	}

	/**
	 * @param string $sWatermarks
	 */
	public function setWatermarks( $sWatermarks ) {
		$this->sWatermarks = $sWatermarks;
	}

	/**
	 * @return string
	 */
	public function getPaperProducer() {
		return $this->sPaperProducer;
	}

	/**
	 * @param string $sPaperProducer
	 */
	public function setPaperProducer( $sPaperProducer ) {
		$this->sPaperProducer = $sPaperProducer;
	}

	/**
	 * @return string
	 */
	public function getPaperProducerInYear() {
		return $this->sPaperProducerInYear;
	}

	/**
	 * @param string $sPaperProducerInYear
	 */
	public function setPaperProducerInYear( $sPaperProducerInYear ) {
		$this->sPaperProducerInYear = $sPaperProducerInYear;
	}

	/**
	 * @return string
	 */
	public function getNotesPublic() {
		return $this->sNotesPublic;
	}

	/**
	 * @param string $sNotesPublic
	 */
	public function setNotesPublic( $sNotesPublic ) {
		$this->sNotesPublic = $sNotesPublic;
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
	 * @param string $sStatus
	 */
	public function setCreated( $sCreated ) {
		$this->sStatus = $sCreated;
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


	/*
	 *
	 */
	public function SetAllPropertiesToNull(){
		foreach( $this as $property => $value) {
			$this->{$property} = null;
		}
	}

}
