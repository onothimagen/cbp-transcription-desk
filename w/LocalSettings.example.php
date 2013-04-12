#############################################################
#############################################################
# CUSTOMISATION SETTINGS
#############################################################
#############################################################

# ENABLING PRETTY URLS (N.B HTACCESS ALSO USED
$wgArticlePath = "/td/$1";
$wgUsePathInfo = true;        # Enable use of pretty URLs


# BP Enabled by default. We need to switch this off because the bentham modern template is not HTML 5
# HTML Tidy will complain that script tags do not contain a 'type' attribute Html->inlineScript()
$wgHtml5 = false;

#############################################################
# USER PERMISSIONS
#############################################################

# Disable for everyone.
$wgGroupPermissions[ '*' ][ 'edit' ]              = false;

# Disable for users, too: by default 'user' is allowed to edit, even if '*' is not.
$wgGroupPermissions[ 'user' ][ 'edit' ]           = false;

# Make it so users with confirmed e-mail addresses are in the group.
$wgAutopromote[ 'emailconfirmed' ]                = APCOND_EMAILCONFIRMED;

# Hide group from user list.
$wgImplicitGroups[]                               = 'emailconfirmed';

# Finally, set it to true for the desired group.
$wgGroupPermissions[ 'emailconfirmed' ][ 'edit' ] = true;

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

$wgNamespaceProtection[ NS_HELP ]    = array( 'edithelp' );
$wgNamespacesWithSubpages[ NS_HELP ] = true;

$wgNamespaceProtection[NS_HELP] = array('edithelp');
$wgNamespacesWithSubpages[NS_HELP] = true;

$wgNamespaceProtection[104]     = array('edittype');
$wgNamespacesWithSubpages[104]  = true;

$wgNamespaceProtection[102]     = array('editproperty');
$wgNamespacesWithSubpages[102]  = true;


#############################################################
# CUSTOM FILE PATHS SETUP
#############################################################

$root_path 	= 'Path to your virtual host';

$transcription_extensions_path 	= $root_path . 'w/extensions/';
$transcription_includes_path 	= $root_path . 'w/includes/';
$transcription_skins_path		= $root_path . 'w/skins/';

$path = array( $transcription_extensions_path
			 , $transcription_includes_path
		     , $transcription_skins_path );

set_include_path( implode( PATH_SEPARATOR, $path ) . PATH_SEPARATOR . get_include_path() );

#############################################################
# EXTENSIONS
#############################################################

############################
# Off the shelf
############################

require_once( 'awc/forums/awc_forum.php' );

require_once( 'DiscussionThreading/DiscussionThreading.php' );
require_once( 'MyVariables/MyVariables.php' );
require_once( 'NoTitle/NoTitle.php' );
require_once( 'Progressbar/Progressbar.php' );
require_once( 'VideoFlash/VideoFlash.php' );
require_once( 'RSSReader/RSSReader.php' );
require_once( 'SocialProfile/SocialProfile.php' );


#require_once( 'WikiEditor/WikiEditor.php' );

#Enables use of WikiEditor by default but still allow users to disable it in preferences
#$wgDefaultUserOptions['usebetatoolbar'] = 1;


##########################################
# ConfirmEdit Extension
##########################################

require_once( 'ConfirmEdit/ConfirmEdit.php' );
require_once( 'ConfirmEdit/ReCaptcha.php' );
$wgCaptchaClass = 'ReCaptcha';

$wgReCaptchaPublicKey = '';
$wgReCaptchaPrivateKey = '';

#########################################################
# Configure Extension (replaces GroupPermissionsManager)
#########################################################

require_once( 'Configure/Configure.php' );
efConfigureSetup();

// Array of editable settings. If this is a non-empty array only the settings in this array will be allowed to be modified
$wgConfigureEditableSettings = array();

$wgGroupPermissions[ '*' ]     [ 'edittype' ]		= false;
$wgGroupPermissions[ '*' ]     [ 'edithelp' ]		= false;
$wgGroupPermissions[ 'sysop' ] [ 'edittype' ]		= true;
$wgGroupPermissions[ 'sysop' ] [ 'edithelp' ]		= true;
$wgGroupPermissions[ 'user' ]  [ 'upload' ]  		= true;

############################
# UserMerge/delete Extension
############################

require_once( 'UserMerge/UserMerge.php' );
$wgGroupPermissions[ 'bureaucrat' ]	[ 'usermerge' ]  	= true;
#optional - default is array( 'sysop' )
$wgUserMergeProtectedGroups = array( 'sysop' );


##########################################
# SemanticWiki Extension and dependencies
##########################################

# DataValues
require_once( 'DataValues/DataValues.php' );
require_once( 'Validator/Validator.php' );

require_once( 'SemanticMediaWiki/SemanticMediaWiki.php' );
enableSemantics('http://www.ac.uk');

$wgGroupPermissions['*']	['editproperty'] = false;
$wgGroupPermissions['sysop']['editproperty'] = true;

############################
# Bespoke
############################


require_once( 'JBZV/JBZV.php' );

$wgJBZVimagesRootDir        = '/zimages';

$wgJBZVTitleOptions = array(
							  'site_name'           => 'Transcribe Bentham'
							, 'regx_pattern'        => '^JB\/(\d\d\d)\/(\d\d\d)\/(\d\d\d)^'
							, 'dir_token_indexes'   => array( 1 )
							, 'name_token_indexes'  => array( 1, 2, 3 )
							, 'file_name_seperator' => '_'
);

require_once( 'JBTEIToolbar/JBTEIToolbar.php' );
require_once( 'TEITags/TEITags.php' );


























