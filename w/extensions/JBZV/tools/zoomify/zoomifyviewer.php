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
*
*/

$lang = 'en';

define( 'MEDIAWIKI', '' );

require '../../JBZV.i18n.php';

$useJSMsg     = $messages[ $lang ][ 'to-use-javascript' ];
$clickHereMsg = $messages[ $lang ][ 'click-here' ];
$insteadMsg   = $messages[ $lang ][ 'instead' ];
$viewerTitle  = $messages[ $lang ][ 'flash-viewer' ];
$errorMsg	  = $messages[ $lang ][ 'error' ];


$requiredGetVars = array( 'image'    => 'imageFilePath'
					    , 'lang'     => 'lang'
		  			    , 'sitename' => 'siteName' );

foreach ( $requiredGetVars as $getVar => $varName ){

	if( isset( $_GET[ $getVar ] ) === TRUE ){
		$$varName = $_GET[ $getVar ];
	}else{
		$errorMsg = sprintf( $errorMsg, $getVar );
		throw new Exception( $errorMsg );
	}

}





?>
<head>
<title>[ <?php echo $siteName; ?> - <?php echo $viewerTitle; ?> ]</title>
<link rel=stylesheet href="zoomifyviewer.css" type="text/css" media=screen>
<body id="main_body">
<object class="z_embed_style"
	classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0"
	id="ZoomifyViewer">
	<param name ="flashvars"
		value="<?php echo $imageFilePath; ?>&zoomifyNavWindow=1"/>
	<param name="menu"
		value="false"/>
	<param name="src"
		value="ZoomifyViewer.swf"/>
	<embed flashvars="zoomifyImagePath=<?php echo $imageFilePath; ?>&zoomifyNavWindow=0"
		src="ZoomifyViewer.swf"
		menu="false"
		pluginspage="http://www.adobe.com/go/getflashplayer"
		type="application/x-shockwave-flash"
		name="ZoomifyViewer"
		class="z_embed_style">
	</embed>
</object>
<p><?php echo $useJSMsg ?> <a href="../ajax-tiledviewer/ajax-tiledviewer.php?&image=<?php echo $imageFilePath; ?>&lang=<?php echo $lang; ?>&sitename=<?php echo $siteName; ?>"><?php echo $clickHereMsg; ?></a> <?php echo $insteadMsg; ?>.</p>
</div>
</body>
</html>
