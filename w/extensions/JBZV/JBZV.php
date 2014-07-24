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

$wgExtensionCredits['jbzv'][] = array(
		'path' 	      =>  __FILE__,
		'name'        => 'JBZVTranscriptionEditor',
		'type'		  => 'parserhook',
		'author'      => 'Richard Davis',
		'url'         => 'http://www.transcribe-bentham.da.ulcc.ac.uk',
		'version'     => '0.2',
		'description' => new Message( 'jbzv-descr' )
);


$wgAutoloadClasses[ 'JBZVHooks' ]    = __DIR__ . '/JBZV.body.php';
$wgExtensionMessagesFiles[ 'JBZV' ]  = __DIR__ . '/JBZV.i18n.php';

$wgResourceModules['ext.JBZV' ] = array(
		'localBasePath' => dirname( __FILE__ ) . '/css',
		'styles'  => 'ext.jbzv.css',
);

$JBZVHooks = new JBZVHooks( );

$wgHooks['EditPage::showEditForm:fields'][] = array( $JBZVHooks, 'onEditPageShowEditFormInitial' );
$wgHooks['ArticlePageDataAfter'][] 			= array( $JBZVHooks, 'onArticlePageDataAfter' );








