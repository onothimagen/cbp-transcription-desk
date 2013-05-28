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

/*
 * See respective Classes/*Task.php for more detailed information
 *
 */


namespace Classes;

ini_set('display_errors', 1);


date_default_timezone_set( 'Europe/London' );

$sJobName = 'Initiate Jobs';

require_once 'header.inc.php';

require_once 'bootstrap.inc.php';

require_once 'Classes/1_InitiateJobTask.php';

$oInitiateJobsTask = new InitiateJobsTask( $oDi, $aSectionConfig );

$oJobQueueEntity = $oInitiateJobsTask->Execute();

if( $oJobQueueEntity === false){
	exit;
}

$sJobId = $oJobQueueEntity->getId();

$sStep = 'Job ' . $sJobId . ' initiated';

$oLogger->Step( $sStep );


/* Initiation of the Job was successful so start import of the CSV */

require '2_ImportCsvIntoDbProcess.php';

require_once 'footer.inc.php';


































