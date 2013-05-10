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

/*
 * Only enable this when viewing as web page ( really should be run from php CLI)
 * Switch off flush in order to get flushing via system() or passthru() call
 *
*/

if( isset ( $_SERVER['HTTP_HOST' ] ) ){

	header( 'Content-Type: text/html; charset=utf-8' );
	header( 'Cache-Control: no-cache, no-store, max-age=0, must-revalidate' );
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
	header( 'Pragma: no-cache' );
	header( 'Access-Control-Allow-Origin: *' );
	@ini_set( 'zlib.output_compression', 0 );

	// Disable apache output buffering/compression
	if (function_exists('apache_setenv')) {
	    apache_setenv('no-gzip', '1');
	    apache_setenv('dont-vary', '1');
	}

	@ini_set( 'implicit_flush', 1 );
	@ini_set( 'output_buffering', 0 );


	while (ob_get_level() > 0) {

	    $level = ob_get_level();

	    ob_end_clean();

	    if (ob_get_level() == $level) break;
	}

	ob_implicit_flush(true);

	ob_start();

	if( isset ( $_SERVER['HTTP_HOST' ] ) ){
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



}

