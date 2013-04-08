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

class Box extends EntityAbstract{


	private $job_queue_id;
	private $box_number;



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



}
