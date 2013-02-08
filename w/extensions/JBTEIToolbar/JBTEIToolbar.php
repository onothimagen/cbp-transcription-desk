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
require_once( "\$IP/extensions/JBTEIToolbar/JBTEIToolbar.php" );
EOT;
	exit( 1 );
}

$wgAutoloadClasses[ 'JBTEIToolbarHooks' ]   = __DIR__ . '/JBTEIToolbar.body.php';
$wgExtensionMessagesFiles[ 'JBTEIToolbar' ] = __DIR__ . '/JBTEIToolbar.i18n.php';

$wgExtensionCredits[ 'jbteitoolbar' ][] = array(
		'path' 	      =>  __FILE__,
		'name'        => 'JBTEIToolbar',
		'author'      => 'Richard Davis',
		'url'         => 'http://www.transcribe-bentham.da.ulcc.ac.uk',
		'version'     => '0.2',
		'description' => 'Extension to add a toolbar supporting TEI tags for transcription purposes'
);


$wgResourceModules['ext.JBTEIToolbar' ] = array(
				'localBasePath' => dirname( __FILE__ ) . '/js',
				'scripts' => 'ext.jbteitoolbar.js',
				'messages' => array(  /* Label text */
									  'toolbar-label-line-break'
									 ,'toolbar-label-page-break'
									 ,'toolbar-label-heading'
									 ,'toolbar-label-paragraph'
									 ,'toolbar-label-addition'
									 ,'toolbar-label-deletion'
									 ,'toolbar-label-questionable'
									 ,'toolbar-label-illegible'
									 ,'toolbar-label-note'
									 ,'toolbar-label-underline'
									 ,'toolbar-label-superscript'
									 ,'toolbar-label-spelling'
									 ,'toolbar-label-foreign'
									 ,'toolbar-label-ampersand'
									 ,'toolbar-label-long-dash'
									 ,'toolbar-label-comment'

									  /* Peri text */
									 ,'toolbar-peri-heading'
									 ,'toolbar-peri-paragraph'
									 ,'toolbar-peri-addition'
									 ,'toolbar-peri-deletion'
									 ,'toolbar-peri-questionable'
									 ,'toolbar-peri-note'
									 ,'toolbar-peri-underline'
									 ,'toolbar-peri-superscript'
									 ,'toolbar-peri-spelling'
									 ,'toolbar-peri-foreign'
									 ,'toolbar-peri-comment'
						)

);


$wgHooks['EditPage::showEditForm:initial'][] = 'JBTEIToolbarHooks::editPageShowEditFormInitial';





