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

namespace Classes;

use Classes\Db\JobQueue;

use Classes\Exceptions\Importer as ImporterException;

$sJobName = 'Import CSV Into DB';

require_once 'html_header.inc.php';

require_once 'global.inc.php';

require_once 'Classes\2_ImportCsvIntoDbTask.php';

$oCsvRowToMetatDataEntityMapper = new Mappers\CsvRowToFolioEntity();

$oImportCsvIntoDbTask           = new ImportCsvIntoDbTask( $oDi
											             , $oCsvRowToMetatDataEntityMapper
														 , $aSectionConfig
														 , $oJobQueueEntity );

echo 'Import from CSV started <br />';

try {
	$oImportCsvIntoDbTask->Execute();
} catch ( ImporterException $oException ) {

	/* @var $oJobQueueDb JobQueue */
	$oJobQueueDb = $oDi->get( 'Classes\Db\JobQueue' );

	$oJobQueueDb->UpdateJobStatus( $iJobQueueId, 'error' );

}

echo 'Import from CSV completed <p />';


/* Import of the Folio and Items was successful so start slicing the images */
require '3_SliceImagesJob.php';


require_once 'footer.inc.php';






























