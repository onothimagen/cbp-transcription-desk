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

class Item extends EntityAbstract{


	private $folio_id;
	private $item_number;


	/**
	 * @param string $folio_id
	 */
	public function setFolioId( $folio_id ) {
		$this->folio_id = $folio_id;
	}

	/**
	 * @return string
	 */
	public function getFolioId() {
		return $this->folio_id;
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



}
