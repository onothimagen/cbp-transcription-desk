<?php

namespace Classes\Mappers;

class CsvRowToMetaDataEntity{

	/* @var Entities_MetaData */
	private $oMetaDataEntity;


	public function __construct( ){

		$this->oMetaDataEntity = new \Classes\Entities\MetaData();

	}

	/*
	 *@return Entities_MetaData
	*/
	public function Map( $aRow ){

		/* @var Entities_MetaData */
		$oMetaDataEntity = $this->oMetaDataEntity;

		/* Reset the MetaDataEntity */
		$oMetaDataEntity->SetAllPropertiesToNull();

		$oMetaDataEntity->setId                        ( (int) $aRow[ 'ID number' ] );
		$oMetaDataEntity->setFolioNumber               ( $aRow[ 'Folio number' ] );
		$oMetaDataEntity->setSecondFolioNumber         ( $aRow[ 'Second folio number' ] );
		$oMetaDataEntity->setCategory                  ( $aRow[ 'Category' ] );
		$oMetaDataEntity->setRectoVerso                ( $aRow[ 'Recto/Verso' ] );
		$oMetaDataEntity->setCreator                   ( $aRow[ 'Creator' ] );
		$oMetaDataEntity->setRecipient                 ( $aRow[ 'Recipient' ] );
		$oMetaDataEntity->setPenner                    ( $aRow[ 'Penner' ] );
		$oMetaDataEntity->setMarginals                 ( $aRow[ 'Marginals' ] );
		$oMetaDataEntity->setCorrections               ( $aRow[ 'Corrections' ] );
		$oMetaDataEntity->setDate_1                    ( $aRow[ 'Date 1' ] );
		$oMetaDataEntity->setDate_2                    ( $aRow[ 'Date 2' ] );
		$oMetaDataEntity->setDate_3                    ( $aRow[ 'Date 3' ] );
		$oMetaDataEntity->setDate_4                    ( $aRow[ 'Date 4' ] );
		$oMetaDataEntity->setDate_5                    ( $aRow[ 'Date 5' ] );
		$oMetaDataEntity->setDate_6                    ( $aRow[ 'Date 6' ] );
		$oMetaDataEntity->setEstimatedDate             ( $aRow[ 'Estimated date' ] );
		$oMetaDataEntity->setInfoInMainHeadingField    ( $aRow[ 'Info in main headings field' ] );
		$oMetaDataEntity->setMainHeading               ( $aRow[ 'Main headings' ] );
		$oMetaDataEntity->setSubHeadings               ( $aRow[ 'Sub-headings' ] );
		$oMetaDataEntity->setMarginalSummaryNumbering  ( $aRow[ 'Marginal summary numbering' ] );
		$oMetaDataEntity->setNumberOfPages             ( $aRow[ 'Number of pages' ] );
		$oMetaDataEntity->setPageNumbering             ( $aRow[ 'Page numbering' ] );
		$oMetaDataEntity->setTitles                    ( $aRow[ 'Titles' ] );
		$oMetaDataEntity->setWatermarks                ( $aRow[ 'Watermarks' ] );
		$oMetaDataEntity->setPaperProducer             ( $aRow[ 'Paper producer' ] );
		$oMetaDataEntity->setPaperProducerInYear       ( $aRow[ 'Paper produced in year' ] );
		$oMetaDataEntity->setNotesPublic               ( $aRow[ 'Notes (public)' ] );
		$oMetaDataEntity->setBoxNumber                 ( $aRow[ 'Box number' ] );
		$oMetaDataEntity->setJobQueueId                ( 1 );
		$oMetaDataEntity->setProcess                   ( 'import' );
		$oMetaDataEntity->setStatus                    ( 'completed' );


		return $oMetaDataEntity;
	}



}
