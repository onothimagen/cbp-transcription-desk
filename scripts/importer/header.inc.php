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


ignore_user_abort(TRUE); // run script in background
set_time_limit(0); // run script forever

function GetUserSpecifiedJobId(){
	global $argv;

	if( isset( $_GET[ 'job_id' ] ) ){
		$iJobId = (int) $_GET[ 'job_id' ];
		return ' ' . $iJobId;
	}elseif ( isset( $argv[ 1 ] ) ){
		$iJobId = (int) $argv[ 1 ];
		return ' ' . $iJobId;
	}
	return '';
}

function GetAction(){
	global $argv;

	if( isset( $_GET[ 'action' ] ) ){
		$sAction = $_GET[ 'action' ];
		return ' ' . $sAction;
	}elseif ( isset( $argv[ 2 ] ) ){
		$sAction = $argv[ 2 ];
		return ' ' . $sAction;
	}
	return '';
}

$iJobId  = GetUserSpecifiedJobId();

$sAction = GetAction();

function disable_ob() {

    // Turn off output buffering
    ini_set('output_buffering', 'off');

    // Turn off PHP output compression
    ini_set('zlib.output_compression', false);

    // Implicitly flush the buffer(s)
    ini_set('implicit_flush', true);

    ob_implicit_flush(true);

    // Clear, and turn off output buffering
    while (ob_get_level() > 0) {
        // Get the curent level
        $level = ob_get_level();
        // End the buffering
        ob_end_clean();
        // If the current level has not changed, abort
        if (ob_get_level() == $level) break;
    }

    // Disable apache output buffering/compression
    if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', '1');
        apache_setenv('dont-vary', '1');
    }
}

if( isset ( $_SERVER['HTTP_HOST' ] ) ){

	/*
	*
	* Disabling output buffering is required for using the passthru() command for real time output in the browser
	*/
	disable_ob();

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
	<link rel="icon" href="./favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
	<title><?php echo $sJobName; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
<?php
}
// NOTE: Do not add carriage returns below, otherwise there will be many blank lines appearing in the console before any output
?>