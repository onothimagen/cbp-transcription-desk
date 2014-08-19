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
* Append this to the end of LocalSettings.php
*
* @package MediaWiki
* @author Ben Parish <b.parish@ulcc.ac.uk>
* @author Richard Davis <r.davis@ulcc.ac.uk>
*/

#############################################################
# CUSTOMISATION SETTINGS
#############################################################

$wgScriptPath       = "/w";

#ENABLING PRETTY URLS (N.B HTACCESS ALSO USED

$wgArticlePath = "/td/$1";
$wgUsePathInfo = true;        # Enable use of pretty URLs

# BP Enabled by default. We need to switch this off because the bentham modern template is not HTML 5
# HTML Tidy will complain that script tags do not contain a 'type' attribute Html->inlineScript()
$wgHtml5 					= false;

$wgExternalLinkTarget  = '_blank';

$wgExtensionAssetsPath = $IP . '/extensions/';

require_once( $wgExtensionAssetsPath . '/WikiEditor/WikiEditor.php' );

##############################################################
# CBP TRANSCRIPTION SKINS
##############################################################

$wgValidSkinNames[ 'benthammodern' ]            = "BenthamModern";
$wgValidSkinNames[ 'cbptranscription' ]         = "CbpTranscription";
$wgValidSkinNames[ 'cbptranscriptionenhanced' ] = "CbpTranscriptionEnhanced";

$wgDefaultSkin = 'cbptranscription';

$wgLocalStylePath = $IP . '/skins/';

require_once( $wgLocalStylePath . 'BenthamModern/BenthamModern.php' );
require_once( $wgLocalStylePath . 'CbpTranscription/CbpTranscription.php' );
require_once( $wgLocalStylePath . 'CbpTranscriptionEnhanced/CbpTranscriptionEnhanced.php' );


##############################################################
# CBP TRANSCRIPTION EXTENSIONS
##############################################################

$wgJBZVimagesRootDir   = '/zimages';

$wgJBZVTitleOptions = array(
                              'site_name'           => 'Transcribe Bentham'
                            , 'regx_pattern'        => '^TD\/(\d\d\d)\/(\d\d\d)\/(\d\d\d)^'
                            , 'dir_token_indexes'   => array( 1 )
                            , 'name_token_indexes'  => array( 1, 2, 3 )
                            , 'file_name_seperator' => '_'
);

require_once( $wgExtensionAssetsPath . '/JBZV/JBZV.php' );
require_once( $wgExtensionAssetsPath . '/JBTEIToolbar/JBTEIToolbar.php' );
require_once( $wgExtensionAssetsPath . '/TEITags/TEITags.php' );


























