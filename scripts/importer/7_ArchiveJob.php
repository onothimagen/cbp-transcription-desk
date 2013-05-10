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

$sJobName = 'Verify Pages';

require_once 'header.inc.php';

require_once 'bootstrap.inc.php';

require_once 'Classes/7_ArchiveTask.php';

$oArchiveTask = new ArchiveTask(  $oDi
                                , $aSectionConfig
                                , $oJobQueueEntity );

$sStep = 'Archiving XML started';
$oLogger->Step( $sStep );

$oArchiveTask->Execute();

$sStep = 'Archiving XML completed';
$oLogger->Step( $sStep );

require_once 'footer.inc.php';


































