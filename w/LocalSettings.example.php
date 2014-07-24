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
* Append this to then end of LocalSettings.php
*
* @package MediaWiki
* @author Ben Parish <b.parish@ulcc.ac.uk>
* @author Richard Davis <r.davis@ulcc.ac.uk>
*/

$wgScriptPath       = "/td";

#############################################################
#############################################################
# CUSTOMISATION SETTINGS
#############################################################
#############################################################

#ENABLING PRETTY URLS (N.B HTACCESS ALSO USED

$wgArticlePath = "/td/$1";
$wgUsePathInfo = true;        # Enable use of pretty URLs

$wgValidSkinNames[ 'benthammodern' ]            = "BenthamModern";
$wgValidSkinNames[ 'cbptranscription' ]         = "CbpTranscription";
$wgValidSkinNames[ 'cbptranscriptionenhanced' ] = "CbpTranscriptionEnhanced";

$wgDefaultSkin = 'benthammodern';

$wgDefaultSkin = 'cbptranscription';

# BP Enabled by default. We need to switch this off because the bentham modern template is not HTML 5
# HTML Tidy will complain that script tags do not contain a 'type' attribute Html->inlineScript()
$wgHtml5 					= false;

$wgExternalLinkTarget = '_blank';


$wgLocalStylePath = $IP . '/skins/';

require_once( $wgLocalStylePath . 'BenthamModern/BenthamModern.php' );
require_once( $wgLocalStylePath . 'CbpTranscription/CbpTranscription.php' );
require_once( $wgLocalStylePath . 'CbpTranscriptionEnhanced/CbpTranscriptionEnhanced.php' );


#############################################################
# USER PERMISSIONS
#############################################################

# Disable for everyone.
# $wgGroupPermissions[ '*' ][ 'edit' ]     			= false;

# Disable for users, too: by default 'user' is allowed to edit, even if '*' is not.
#$wgGroupPermissions[ 'user' ][ 'edit' ]    			= false;

# Make it so users with confirmed e-mail addresses are in the group.
$wgAutopromote[ 'emailconfirmed' ] 				= APCOND_EMAILCONFIRMED;

# Hide group from user list.
$wgImplicitGroups[] 							= 'emailconfirmed';

# Finally, set it to true for the desired group.
$wgGroupPermissions[ 'emailconfirmed' ][ 'edit' ] 	= true;

#############################################################
# DEFINE NAMESPACES AND PROTECT FROM EDITING
#############################################################


define("NS_METADATA", 100);
define("NS_METADATA_TALK", 101);

$wgExtraNamespaces[NS_METADATA]      = 'Metadata';
$wgExtraNamespaces[NS_METADATA_TALK] = 'Metadata_talk';
$wgNamespaceProtection[NS_METADATA]  = array( 'editmetadata' ); #permission "editmetaata" required to edit the Metadata namespace

$wgGroupPermissions['sysop']['editmetadata'] = true;      #permission "editmetadata" granted to users in the "sysop" group

#Set default searching
$wgNamespacesToBeSearchedDefault = array(
                                        NS_MAIN           => true,
                                        NS_TALK           => false,
                                        NS_USER           => false,
                                        NS_USER_TALK      => false,
                                        NS_PROJECT        => false,
                                        NS_PROJECT_TALK   => false,
                                        NS_IMAGE          => false,
                                        NS_IMAGE_TALK     => false,
                                        NS_MEDIAWIKI      => false,
                                        NS_MEDIAWIKI_TALK => false,
                                        NS_TEMPLATE       => false,
                                        NS_TEMPLATE_TALK  => false,
                                        NS_HELP           => false,
                                        NS_HELP_TALK      => false,
                                        NS_CATEGORY       => false,
                                        NS_CATEGORY_TALK  => false,
                                        NS_METADATA       => true,
                                        NS_METADATA_TALK  => false
                                    );

$wgGroupPermissions[ '*' ]    [ 'edittype' ] = false;
$wgGroupPermissions[ '*' ]    [ 'edithelp' ] = false;
$wgGroupPermissions[ 'sysop' ][ 'edittype' ] = true;
$wgGroupPermissions[ 'sysop' ][ 'edithelp' ] = true;
$wgGroupPermissions[ 'user' ] [ 'upload' ]   = true;

$wgNamespaceProtection[ NS_HELP ] 	 = array( 'edithelp' );
$wgNamespacesWithSubpages[ NS_HELP ] = true;


$wgNamespaceProtection[NS_HELP]    = array('edithelp');
$wgNamespacesWithSubpages[NS_HELP] = true;

##############################################################
# CBP TRANSCRIPTION EXTENSIONS
##############################################################

$wgExtensionAssetsPath = $IP . '/extensions/';

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