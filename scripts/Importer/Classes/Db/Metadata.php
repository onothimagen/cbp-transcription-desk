<?php

namespace Classes\Db;

use \Classes\Entities\MetaData as MetaDataEntity;
use \Zend\Db\ResultSet\ResultSet;

class MetaData extends DbAbstract{


	const DBNAME = 'cbp_metadata';

	public function Truncate(){

		$sSql = 'TRUNCATE TABLE ' . self::DBNAME . ';';

		$this->Execute( $sSql );
	}

	/*
	 * @return
	 */
	public function Insert( MetaDataEntity $oMetaDataEntity ){

		$sStatus = $oMetaDataEntity->getStatus();
		$sProcess = $oMetaDataEntity->getProcess();

		if( $sStatus == 'started' ){
			$oMetaDataEntity->setStarted( 'NOW()' );
		}


		if( $sProcess == 'verify' and $sStatus == 'completed' ){
			$oMetaDataEntity->setCompleted( 'NOW()' );
		}

		if( $oMetaDataEntity->getUpdated() == NULL ){
			$oMetaDataEntity->setUpdated( 'NULL' );
		}

		if( $oMetaDataEntity->getCompleted() == NULL ){
			$oMetaDataEntity->setCompleted( 'NULL' );
		}

		$sSql = 'INSERT INTO
					' . self::DBNAME . '
								(    id
									,box_number
									,folio_number
									,second_folio_number
									,category
									,recto_verso
									,creator
									,recipient
									,penner
									,marginals
									,corrections
									,date_1
									,date_2
									,date_3
									,date_4
									,date_5
									,date_6
									,estimated_date
									,info_in_main_heading_field
									,main_heading
									,sub_headings
									,marginal_summary_numbering
									,number_of_pages
									,page_numbering
									,titles
									,watermarks
									,paper_producer
									,paper_producer_in_year
									,notes_public
									,job_queue_id
									,process
									,status
									,updated
									,completed
								)
				VALUES
								(
									 ?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,?
									,\'import\'
									,\'completed\'
									,NOW()
									,NOW()
								)';

		$aBindArray = array (  (int) $oMetaDataEntity->getId()
							 , $oMetaDataEntity->getBoxNumber()
							 , $oMetaDataEntity->getFolioNumber()
							 , $oMetaDataEntity->getSecondFolioNumber()
							 , $oMetaDataEntity->getCategory()
							 , $oMetaDataEntity->getRectoVerso()
							 , $oMetaDataEntity->getCreator()
							 , $oMetaDataEntity->getRecipient()
							 , $oMetaDataEntity->getPenner()
							 , $oMetaDataEntity->getMarginals()
							 , $oMetaDataEntity->getCorrections()
							 , $oMetaDataEntity->getDate_1()
							 , $oMetaDataEntity->getDate_2()
							 , $oMetaDataEntity->getDate_3()
							 , $oMetaDataEntity->getDate_4()
							 , $oMetaDataEntity->getDate_5()
							 , $oMetaDataEntity->getDate_6()
							 , $oMetaDataEntity->getEstimatedDate()
							 , $oMetaDataEntity->getInfoInMainHeadingField()
							 , $oMetaDataEntity->getMainHeading()
							 , $oMetaDataEntity->getSubHeadings()
							 , $oMetaDataEntity->getMarginalSummaryNumbering()
							 , $oMetaDataEntity->getNumberOfPages()
							 , $oMetaDataEntity->getPageNumbering()
							 , $oMetaDataEntity->getTitles()
							 , $oMetaDataEntity->getWatermarks()
							 , $oMetaDataEntity->getPaperProducer()
							 , $oMetaDataEntity->getPaperProducerInYear()
							 , $oMetaDataEntity->getNotesPublic()
							 , $oMetaDataEntity->getJobQueueId()
							);

		$this->Execute( $sSql, $aBindArray );

		return $oMetaDataEntity;

	}


	public function UpdateProcessStatus(  $iId
										, $sProcess
										, $sStatus ){

		$sCompleted = 'NULL';

		if ( $sProcess === 'verify' and $sStatus === 'completed' ){
			$sCompleted = 'NOW()';
		}

		$sSql = 'UPDATE
					' . self::DBNAME . '

				SET
					  process = ?
					, status  = ?
				    , updated = NOW()
				    , completed = ' . $sCompleted . '
				WHERE
					id = ?
				';


		$aBindArray = array (
							  $sProcess
							, $sStatus
				            , $iId
							);
		$this->Execute( $sSql, $aBindArray );

		return;

	}


	/*
	 * @param string $sJobQueueId
	 * @return ResultSet
	 */
	public function GetJobMetaData( $sJobQueueId
								  , $sProcess
								  , $sStatus ){

		$sSql = 'SELECT
					*
				FROM
					' . self::DBNAME . '
				WHERE
					job_queue_id = ?
				AND
					process      = ?
				AND
					status       = ?;';


		$aBindArray = array(
							  $sJobQueueId
							, $sProcess
							, $sStatus
							);

		$rResult   = $this->Execute( $sSql, $aBindArray );


		return $rResult;
	}


	/*
	* @param integer $iJobQueueId
	* @param string $sProcess
	* @param string $sStatus
	* @return ResultSet
	*/
	public function GetJobMetaDataItems(  $iJobQueueId
										, $sProcess
										, $sStatus ){

		$sSql = 'SELECT
					  cbp_items.id as item_id
					, cbp_metadata.box_number
					, cbp_metadata.folio_number
					, cbp_items.item_number
				FROM
					cbp_metadata
				LEFT JOIN
					cbp_items
				ON
				    cbp_metadata.id           = cbp_items.metadata_id
				WHERE
					cbp_metadata.job_queue_id = ?
				AND
					cbp_items.process         = ?
				AND
					cbp_items.status          = ?;';


		$aBindArray = array(
							  $iJobQueueId
							, $sProcess
							, $sStatus
		);

		$rResult   = $this->Execute( $sSql, $aBindArray );

		return $rResult;
	}


}

































