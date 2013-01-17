<html>
<?php

function browser_is_ie() 
{ 
  $u_agent = $_SERVER['HTTP_USER_AGENT']; 
  if(preg_match('/MSIE/i',$u_agent)) 
    { 
      return TRUE; 
    } 
  else 
    {
      return FALSE;
    }
}

$browser_is_ie = browser_is_ie();

$view = $_GET['view'];
$item = $_GET['item'];
$ztdir = "./zoomtools";
$img_path = "http://www.transcribe-bentham.da.ulcc.ac.uk/td/Zimages";
$img_dir = substr($item, 0, 3);
$me = "zviewer.php";

$js_header = <<<JSHEADER
<head>
<title>$item [Transcribe Bentham - Javascript viewer]</title>
<style type="text/css">
div#Thumb0 {display:block;position:absolute;bottom:40px;right:10;text-align:center; width:200px;height:150px;overflow:hidden;}
div#Thumb {display:none;position:absolute;right:0px;top:0px;border:1px solid blue;overflow:hidden;}
div#Thumb2 {display:none;position:absolute;border:1px solid cyan;overflow:hidden;}
div#wheelMode {display:none;position:absolute;left:10px;top:260px;overflow:hidden;font-size:11px;font-family:sans-serif;}
div#outerDiv0 {position:absolute;top:0px;left:0px;height:100%;width:100%;border:0px solid black;overflow:hidden;}
div#overlay {width:100px;height:20px;position:absolute;top:145px;left:0;padding-top:0;z-index:100;color:#000;font-size:12px;font-family:sans-serif;border:0 dotted #000;text-align:center;}
#theScale { visibility: hidden; }
#main_body { margin: 0; padding: 0; }
#Nav { display:none;position: absolute; top: 70px; left: 20px; z-index: 1; }
#outerDiv { position:relative;height:95%;width:100%;border:0;overflow:hidden; }
#z_link { position:absolute; bottom: 10px; left: 10px; }
</style>
<script src="$ztdir/ajax-tiledviewer.js" type="text/javascript"></script>
<script type="text/javascript">
//EXAMPLE SHOWING HOW TO SET JAVASCRIPT VARIABLES FOR USE IN THE BRAIN MAPS API.  
//UNCOMMENT THE LINE BELOW TO USE THESE VARIABLE VALUES OR ADD YOUR OWN VALUES.
path="$img_path/$img_dir/$item/"; width=2731; height=4096
; 
</script>
</head>   
JSHEADER;

$zv_header = <<<ZVHEADER
<head>
<title>$item [Transcribe Bentham - Flash viewer]</title>
<style>
#main_body { margin: 0; padding: 0; }
.z_object_style { display:block; margin: auto; width: 100%; height: 90%; }
.z_embed_style { width: 100%; height: 90%; }
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
ZVHEADER;

$js_body = <<<JSBODY
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
        <div id="innerDiv" 
             style="position:relative;top:0px;left:0px;z-index:0;"><noscript><big><b><br><br><br><br><br><br><br><br><br><blockquote>JavaScript must be enabled in order for you to use the BrainMaps API.</b>  However, it seems JavaScript is either disabled or not supported by your browser.   To view this page, enable JavaScript by changing your browser options, and then try again.</big></noscript>        
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
<div id="z_link">To try the flash viewer <a href="$me?view=zv&item=$item">click here</a> instead.</div>
    </body>
JSBODY;

$zv_body = <<<ZVBODY
<body id="main_body">
       <object class="z_embed_style"
               classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" 
               codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" 
               id="ZoomifyViewer">
           <param name="flashvars" 
                  value="$img_path/$img_dir/$item/&zoomifyNavWindow=1"/>
           <param name="menu" 
                  value="false"/>
           <param name="src" 
                  value="zoomtools/ZoomifyViewer.swf"/>
           <embed flashvars="zoomifyImagePath=$img_path/$img_dir/$item/&zoomifyNavWindow=0" 
                  src="zoomtools/ZoomifyViewer.swf" 
                  menu="false" 
                  pluginspage="http://www.adobe.com/go/getflashplayer" 
                  type="application/x-shockwave-flash"  
                  name="ZoomifyViewer" 
                  class="z_embed_style">
            </embed>
        </object>
        <p>To use the Javascript viewer <a href="$me?view=js&item=$item">click here</a> instead.</p>
</body>
ZVBODY;


if ($view == "js") {
  echo $js_header;
  echo $js_body;
} elseif ($view == "zv") {
  echo $zv_header;
  echo $zv_body;
} elseif ( $browser_is_ie ) {
  echo $js_header;
  echo $js_body;
} else {
  echo $zv_header;
  echo $zv_body;
}

?>
</html>