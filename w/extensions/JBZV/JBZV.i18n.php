<?php

/**
 * Copyright (C) 2013 Richard Davis
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
* @package MediaWiki
* @subpackage Extensions
* @author Richard Davis <r.davis@ulcc.ac.uk>
* @author Ben Parish <b.parish@ulcc.ac.uk>
* @copyright 2013 Richard Davis
*/

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/JBZV/JBZV.php" );
EOT;
	exit( 1 );
}


$messages       = array();

$messages['en'] = array(  'jbzv'               => 'JB ZV'
						, 'jbzv-descr'         => 'Extension to add an image to the edit page for transcription purposes'
						, 'flash-viewer'	   => 'Flash viewer'
						, 'javascript-viewer'  => 'JavaScript viewer'
						, 'to-use-javascript'  => 'To use the Javascript viewer'
						, 'to-use-flash'       => 'To use the Flash viewer'
						, 'click-here'         => 'click here'
						, 'instead'            => 'instead'
						, 'javascript-warning' => 'JavaScript must be enabled in order for you to use the BrainMaps API.</b>  However, it seems JavaScript is either disabled or not supported by your browser.   To view this page, enable JavaScript by changing your browser options, and then try again.'
						, 'error'		       => 'The %s parameter is missing in the URL'
						);

$messages[ 'en-gb' ] = $messages[ 'en' ];