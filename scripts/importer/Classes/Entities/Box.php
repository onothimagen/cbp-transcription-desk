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

class Box extends EntityAbstract{

	/* Ordinarily these would be private but need to be public for the installer Zend paginator */

	public $job_queue_id;
	public $box_number;

	/**
	 * @return string
	 */
	public function getJobQueueId() {
		return $this->job_queue_id;
	}

	/**
	 * @param string $job_queue_id
	 */
	public function setJobQueueId( $job_queue_id ) {
		$this->job_queue_id = $job_queue_id;
	}

	/**
	 * @param string $box_number
	 */
	public function setBoxNumber( $box_number ) {
		$this->box_number = $box_number;
	}

	/**
	 * @return string
	 */
	public function getBoxNumber() {
		return $this->box_number;
	}

	/*
	 * Required for the for the installer Zend paginator
	 *
	 * @return void
	*/
	public function exchangeArray( $data ){
		parent::exchangeArray( $data );
		$this->job_queue_id = ( !empty( $data[ 'job_queue_id' ] ) ) ? $data[ 'job_queue_id' ] : null;
		$this->box_number  = ( !empty( $data[ 'box_number' ] ) ) ? $data[ 'box_number' ] : null;
	}

}








































