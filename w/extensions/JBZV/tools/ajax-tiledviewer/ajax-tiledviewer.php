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

$lang = 'en';

if( isset( $_GET[ 'lang' ] ) === TRUE ){
	$lang = $_GET[ 'lang' ];
}

define( 'MEDIAWIKI', '' );

require '../../JBZV.i18n.php';

$useFlashMsg  = $messages[ $lang ][ 'to-use-flash' ];
$clickHereMsg = $messages[ $lang ][ 'click-here' ];
$insteadMsg   = $messages[ $lang ][ 'instead' ];
$viewerTitle  = $messages[ $lang ][ 'javascript-viewer' ];
$jsWarningMsg = $messages[ $lang ][ 'javascript-warning' ];

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

$width  = 2731;
$height = 4096;

if( isset( $_GET[ 'width' ] ) === TRUE ){
	$width = (int) $_GET[ 'width' ];
}

if( isset( $_GET[ 'height' ] ) === TRUE ){
	$height = (int) $_GET[ 'height' ];
}


?>
<head>
<title>[ <?php echo $siteName; ?> - <?php echo $viewerTitle; ?> ]</title>
<link rel=stylesheet href="ajax-tiledviewer.css" type="text/css" media=screen>
</style>
<script type="text/javascript">
//EXAMPLE SHOWING HOW TO SET JAVASCRIPT VARIABLES FOR USE IN THE BRAIN MAPS API.
//UNCOMMENT THE LINE BELOW TO USE THESE VARIABLE VALUES OR ADD YOUR OWN VALUES.
path   = "<?php echo $imageFilePath; ?>";
width  = "<?php echo $width; ?>";
height = "<?php echo $height; ?>";

</script>
<script src="ajax-tiledviewer.js" type="text/javascript"></script>
</head>
<body id="main_body" onload="init()">
    <div id="outerDiv0">
      <div id="outerDiv">
        <div style="position: absolute; top: 10px; left: 10px; z-index: 1">
          <img src="zoomin_off.gif"
               onclick="ZoomIn()"/>
        </div>
		<div style="position: absolute; top: 10px; left: 90px; z-index: 1">
          <img src="zoomout_off.gif"
               onclick="ZoomOut()"/>
        </div>
        <div id="Nav">
	    <div>
                <a href="javascript:slidePrev();"
		   id="slidePrev"><img src="prev.gif" border="0"/></a>
            </div>
	    <div style="position: absolute; top: 0px; left: 79px;">
	        <a href="javascript:slideNext();" id="slideNext"><img src="next.gif" border="0"/></a>
	    </div>
	</div>
	<div id="copy"
	     style="position: absolute; bottom: 10px; left: 10px; z-index: 1"><a href="http://brainmaps.org/index.php?p=brain-maps-api" target="_blank" style="text-decoration:none;"><font face="sans-serif" color="blue" size="-1">Powered by BrainMaps API</font></a>
        </div>
        <div id="innerDiv" style="position:relative;top:0px;left:0px;z-index:0;">
        	<noscript>
        	<big><b><br><br><br><br><br><br><br><br><br><blockquote><?php echo $jsWarningMsg; ?></big></noscript>
            <div id="imageTiles" style="position:relative;top:0;left:0;z-index:0;width:100%;"></div>
	    <div id="imageLabels" style="position:relative;top:0;left:0;z-index:1;width:900000px;height:900000px;"></div>
        </div>
	<div id="overlay">
            <div id="theScale"></div>
            <div id="theInfo"></div>
        </div>
      </div>
      <div id="Thumb0">
          <div id="Thumb"></div>
	  <div id="Thumb2"></div>
      </div>
    </div>
    <div id="wheelMode"><b>Mouse Wheel:</b><br><input type="radio" checked  onClick="wheelMode1()">&nbsp;Zoom<br><input type="radio" onClick="wheelMode2()" >&nbsp;Next/Prev</div>
    <div id="coords" style="position:absolute;top:2px;right:10px;z-index:10;">
    </div>
<div id="z_link"><?php echo $useFlashMsg; ?> <a href="../zoomify/zoomifyviewer.php?&image=<?php echo $imageFilePath; ?>&lang=<?php echo $lang; ?>&sitename=<?php echo str_replace( ' ', '%20', $siteName ); ?>"><?php echo $clickHereMsg; ?></a> <?php echo $insteadMsg; ?>.</div>
</body>