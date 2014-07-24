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

$sJobName = 'Import XML into MW';

require_once 'header.inc.php';

require_once 'bootstrap.inc.php';

require_once 'Classes/5_ImportXmlIntoMwJobTask.php';

$sStep = 'Importing XML into MW started';;
$oLogger->Step( $sStep );

$oImportXmlIntoMwTask = new ImportXmlIntoMwJobTask( $oDi
												  , $aSectionConfig
												  , $oJobQueueEntity );

$oImportXmlIntoMwTask->Execute();

$sStep = 'Importing XML into MW completed';
$oLogger->Step( $sStep );

require_once '6_VerifyPagesProcess.php';

require_once 'footer.inc.php';



































