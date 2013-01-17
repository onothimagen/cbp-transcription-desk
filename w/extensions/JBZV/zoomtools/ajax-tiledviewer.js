//Written by Shawn Mikula, 2007.   Contact: brainmaps--at--gmail.com.   You are free to use this software for non-commercial use only, and only if proper credit is clearly visibly given wherever the code is used. 

winsize(); 
window.onresize=winsize; 
var tileSize = 256; var center=0; var height; var width; var path; var imgpath; var labelspath; var loadedXML=0; var getJSON=0; var loadedJSON=0; var JSON; var JSONnum; var JSONout; var slidePointer=0; var wheelmode=0; var coords=0; 

start=getVar('start'); if (start.length>0){ slidePointer=start;}
var innerDiv; vTier2 = getVar('vT'); if (vTier2.length>0){ var zoom=vTier2;}else{ var zoom = 2;}
var dragging = false; var mTop; var mLeft; var dragStartTop; var dragStartLeft; 

function init() { var imageLabels=$("imageLabels"); var divs = imageLabels.getElementsByTagName("div"); while (divs.length > 0) imageLabels.removeChild(divs[0]); path2 =getVar('path'); width2=getVar('width'); height2=getVar('height'); coords2=getVar('coords'); if (path2.length>1) path=path2; if (width2.length>0) width=width2; if (height2.length>0) height=height2; if (coords2.length>0) coords=coords2; JSON=getVar('JSON'); if (JSON.length>1 && !loadedJSON){ getJSON=1; document.getElementById("Nav").style.display="block"; loadJSON(); $('wheelMode').style.display="block";}
res =getVar('res'); if (res.length<1){ res=0.46;}
resunits =getVar('resunits'); if (resunits.length<1){ resunits="um";}
if (width2.length<1 && !loadedXML && !getJSON){ loadXMLfile();}
imgpath=path; gImageWidth=width; gImageHeight=height; tempWidth = gImageWidth; tempHeight =gImageHeight; var j; divider=2; gTierCount = 1; while (tempWidth>tileSize || tempHeight>tileSize){ tempWidth = Math.floor(gImageWidth/divider)
tempHeight = Math.floor(gImageHeight/divider); divider*=2; if(tempWidth%2) tempWidth++; if(tempHeight%2) tempHeight++; gTierCount++;}
var innerDiv = document.getElementById("innerDiv"); if ((getVar('vX').length)>0){ center=1; innerDiv.style.left=-getVar('vX')*gImageWidth/(Math.pow(2,gTierCount-1-zoom)) +viewportWidth/2+"px"; innerDiv.style.top=-getVar('vY')*gImageHeight/(Math.pow(2,gTierCount-1-zoom))+viewportHeight/2+"px";}else{ innerDiv.style.left=-(width/(Math.pow(2,gTierCount-1-zoom)) )/2+viewportWidth/2; innerDiv.style.top=-(height/(Math.pow(2,gTierCount-1-zoom)))/2+viewportHeight/2; center=1;}
hidethumb =getVar('hidethumb'); if (hidethumb.length>0){ }else{ showThumb();}
var outerDiv = document.getElementById("outerDiv"); outerDiv.onmousedown = startMove; outerDiv.onmousemove = processMove; outerDiv.onmouseup = stopMove; outerDiv.ondragstart = function() { return false;}
checkTiles(); updateZoom();}


function startMove(event) { if (!event){ event = window.event;}
dragStartLeft = event.clientX; dragStartTop = event.clientY; mTop = stripPx(innerDiv.style.top); mLeft = stripPx(innerDiv.style.left); dragging = true; return false;}


function processMove(event) { if (!event){ event = window.event;}
if (coords){ $('coords').innerHTML=Math.round(((-stripPx(innerDiv.style.left)+event.clientX-0)/(gImageWidth/(Math.pow(2,gTierCount-1-zoom)))*1000))/1000 + ", "+Math.round(((-stripPx(innerDiv.style.top)+event.clientY-16)/(gImageHeight/(Math.pow(2,gTierCount-1-zoom)))*1000))/1000;}
if (dragging) { innerDiv.style.top = mTop + (event.clientY - dragStartTop); innerDiv.style.left = mLeft + (event.clientX - dragStartLeft);}
checkTiles();}


function checkTiles() { innerDiv = document.getElementById("innerDiv"); var visibleTiles = getVisibleTiles(); var visibleTilesMap = {}; for (i = 0; i < visibleTiles.length; i++) { var tileArray = visibleTiles[i]; gTileCountWidth=new Array(); gTileCountHeight=new Array(); tempWidth = gImageWidth; tempHeight = gImageHeight; divider=2; for (var j=gTierCount-1; j>=0; j--) { gTileCountWidth[j] = Math.floor(tempWidth/tileSize); if (tempWidth%tileSize){ gTileCountWidth[j]++;}
gTileCountHeight[j] = Math.floor(tempHeight/tileSize); if (tempHeight%tileSize){ gTileCountHeight[j]++;}
tempWidth = Math.floor(gImageWidth/divider); tempHeight = Math.floor(gImageHeight/divider); divider*=2; if(tempWidth%2) tempWidth++; if(tempHeight%2) tempHeight++;}
if (!center){ innerDiv.style.left=-(width/(Math.pow(2,gTierCount-1-zoom)) )/2+viewportWidth/2; innerDiv.style.top=-(height/(Math.pow(2,gTierCount-1-zoom)))/2+viewportHeight/2; center=1;}
moveThumb2(); pCol=tileArray[0]; pRow=tileArray[1]; tier=zoom; var theOffset=parseFloat(pRow*gTileCountWidth[tier]+pCol); for (var theTier=0; theTier<tier; theTier++) theOffset += gTileCountWidth[theTier]*gTileCountHeight[theTier]; _tileGroupNum=Math.floor(theOffset/256.0); if (pCol<gTileCountWidth[zoom] && pRow<gTileCountHeight[zoom]){ var tileName = "TileGroup" + _tileGroupNum + "/" + zoom + "-" + pCol + "-" + pRow + ".jpg";}
visibleTilesMap[tileName] = true; var img = document.getElementById(tileName); if (!img) { img = document.createElement("img"); img.src = imgpath + tileName; img.style.position = "absolute"; img.style.left = (tileArray[0] * tileSize) + "px"; img.style.top = (tileArray[1] * tileSize) + "px"; img.style.zIndex = 0; img.setAttribute("id", tileName); imageTiles.appendChild(img);}
}
var imgs = imageTiles.getElementsByTagName("img"); for (i = 0; i < imgs.length; i++) { var id = imgs[i].getAttribute("id"); if (!visibleTilesMap[id]) { imageTiles.removeChild(imgs[i]); i--;}
}
}


function getVisibleTiles() { var mapX = stripPx(innerDiv.style.left); var mapY = stripPx(innerDiv.style.top); var startX = Math.abs(Math.floor(mapX / tileSize)) - 2; var startY = Math.abs(Math.floor(mapY / tileSize)) -1; var tilesX = Math.ceil(viewportWidth / tileSize) +2; var tilesY = Math.ceil(viewportHeight / tileSize) +1; var visibleTileArray = []; var counter = 0; for (x = startX; x < (tilesX + startX); x++) { for (y = startY; y < (tilesY + startY); y++) { if (x>=0 && y>=0){ visibleTileArray[counter++] = [x, y];}
}
}
return visibleTileArray;}


function stopMove() { dragging = false;}


function ZoomIn() { if (zoom!=gTierCount-1){ mTop = stripPx(innerDiv.style.top); mLeft = stripPx(innerDiv.style.left); innerDiv.style.top = 2*mTop-viewportHeight/2; innerDiv.style.left = 2*mLeft-viewportWidth/2; zoom=zoom+1; var imgs = imageTiles.getElementsByTagName("img"); while (imgs.length > 0) imageTiles.removeChild(imgs[0]); checkTiles(); var divs = imageLabels.getElementsByTagName("div"); for (var $i = 0; $i <divs.length; $i++){ var Ltemp="L"+$i; $(Ltemp).style.top=2*stripPx($(Ltemp).style.top); $(Ltemp).style.left=2*stripPx($(Ltemp).style.left);}
}
updateZoom();}


function ZoomOut() { if (zoom!=0){ mTop = stripPx(innerDiv.style.top); mLeft = stripPx(innerDiv.style.left); innerDiv.style.top = mTop/2 + viewportHeight/4; innerDiv.style.left = mLeft/2 + viewportWidth/4; zoom=zoom-1; var imgs = imageTiles.getElementsByTagName("img"); while (imgs.length > 0) imageTiles.removeChild(imgs[0]); checkTiles(); var divs = imageLabels.getElementsByTagName("div"); for (var $i = 0; $i <divs.length; $i++){ var Ltemp="L"+$i; $(Ltemp).style.top=.5*stripPx($(Ltemp).style.top); $(Ltemp).style.left=.5*stripPx($(Ltemp).style.left);}
}
updateZoom();}


function updateZoom() { if (JSONnum){ $('theScale').innerHTML=Math.pow(2,gTierCount-1-zoom)*res*50 + "  " + resunits + "<br><br>zoom level: " + zoom + "/" + (gTierCount-1) + "<br>slide #: " + (slidePointer+1) + "/" + JSONnum ;}else{ $('theScale').innerHTML=Math.pow(2,gTierCount-1-zoom)*res*50 + "  " + resunits + "<br><br>zoom level: " + zoom + "/" + (gTierCount-1);}
}


function showThumb(){ var Thumb=$('Thumb');Thumb.innerHTML='<img src="' + imgpath + 'TileGroup0/0-0-0.jpg">';$('Thumb0').style.height=gImageHeight/(Math.pow(2,gTierCount-1))+2; $('Thumb0').style.width=gImageWidth/(Math.pow(2,gTierCount-1))+3; Thumb.style.display="block"; $('Thumb2').style.display="block"; Thumb.onmouseup = clickThumb; Thumb.ondragstart = function() { return false;}
}


function hideThumb(){ $('Thumb').style.display="none";}


function moveThumb2(){ var Thumb2 = $("Thumb2"); topT = stripPx(innerDiv.style.top); leftT = stripPx(innerDiv.style.left); Thumb2.style.width=viewportWidth/Math.pow(2,zoom); Thumb2.style.height=viewportHeight/Math.pow(2,zoom); Thumb2.style.left=-leftT/(Math.pow(2,zoom)); Thumb2.style.top=-topT/(Math.pow(2,zoom));}


function clickThumb(event){ if (event){ xThumb = event.clientX; yThumb = event.clientY; var innerDiv = $("innerDiv"); innerDiv.style.left=-(1-Math.abs(viewportWidth-xThumb)/stripPx($("Thumb0").style.width))*width/(Math.pow(2,gTierCount-zoom-1)); innerDiv.style.top=-(1-Math.abs(viewportHeight-yThumb)/stripPx($("Thumb0").style.height))*height/(Math.pow(2,gTierCount-zoom-1)); moveThumb2()
}
}


function wheelMode1(){ $('wheelMode').innerHTML='<b>Mouse Wheel:</b><br><input type="radio" checked  onClick="wheelMode1()">&nbsp;Zoom<br><input type="radio" onClick="wheelMode2()" >&nbsp;Next/Prev'; wheelmode=0;}


function wheelMode2(){ $('wheelMode').innerHTML='<b>Mouse Wheel:</b><br><input type="radio" onClick="wheelMode1()">&nbsp;Zoom<br><input type="radio" checked  onClick="wheelMode2()" >&nbsp;Next/Prev'; wheelmode=1;}


function loadJSON(){ JSONrequest = getHTTPObject(); JSONrequest.onreadystatechange = JSONread; JSONrequest.open("GET", JSON, true); JSONrequest.send(null);}


function JSONread() { 
	if (JSONrequest.readyState == 4) { 
		JSONout = eval('('+JSONrequest.responseText+')'); 			  
		JSONnum=JSONout.slides.length; 
		path = JSONout.slides[slidePointer].path; 
		width = JSONout.slides[slidePointer].width; 
		height = JSONout.slides[slidePointer].height; 
		if (JSONout.slides[slidePointer].labelspath!=undefined){ 		
			labelspath=JSONout.slides[slidePointer].labelspath; 
			loadLabels();
		}
		loadedJSON=1; 
		init();
	}
}
labels = getHTTPObject(); 

function loadLabels(){ var urlLabels = labelspath; var pinImage = $("L0"); if (pinImage) { imageLabels = $("imageLabels"); var divs = imageLabels.getElementsByTagName("div"); while (divs.length > 0) imageLabels.removeChild(divs[0]);}else{ labels.open("GET", urlLabels, true); labels.onreadystatechange = labelsHandler; labels.send(null);}
}


function labelsHandler() { 
	if (labels.readyState == 4 ) { 
		var labels2 = eval('('+labels.responseText+')'); 
		var lab=labels2.labels.length; 
		for (var $i = 0; $i < lab; $i++){ 
			var label = labels2.labels[$i].label; 
			var name = labels2.labels[$i].name; 
			var nX = labels2.labels[$i].x; 
			var nY = labels2.labels[$i].y; 
			if (labels2.labels[$i].url!=undefined){ 		
				label='<a href="'+labels2.labels[$i].url+'" target="_blank">'+label+'</a>'; 
			}
			pinImage = document.createElement("div"); 
			pinImage.style.position = "absolute"; 
			pinImage.style.left =(nX*gImageWidth/(Math.pow(2,gTierCount-1-zoom))) +"px"; 
			pinImage.style.top =(nY*gImageHeight/(Math.pow(2,gTierCount-1-zoom))) +"px"; 
			pinImage.style.width = 8*label.length + "px"; pinImage.style.height = "2px"; 
			pinImage.style.zIndex = 1; 
			pinImage.setAttribute("id", "L"+$i); 
			pinImage.innerHTML= label; 
			$("imageLabels").appendChild(pinImage);}
	}
}


function refreshTiles() { var imageTiles = $("imageTiles"); var imgs = imageTiles.getElementsByTagName("img"); while (imgs.length > 0) imageTiles.removeChild(imgs[0]);}


function slideNext(){ if (slidePointer<JSONnum-1){ slidePointer++;}else{ slidePointer=0;}
path = JSONout.slides[slidePointer].path; width = JSONout.slides[slidePointer].width; height = JSONout.slides[slidePointer].height; if (JSONout.slides[slidePointer].labelspath!=undefined){ labelspath=JSONout.slides[slidePointer].labelspath; loadLabels();}else{labelspath="";}
init(); refreshTiles(); checkTiles();}


function slidePrev(){ if (slidePointer>0){ slidePointer--;}else{ slidePointer=JSONnum-1;}
path = JSONout.slides[slidePointer].path; width = JSONout.slides[slidePointer].width; height = JSONout.slides[slidePointer].height; if (JSONout.slides[slidePointer].labelspath!=undefined){ labelspath=JSONout.slides[slidePointer].labelspath; loadLabels();}else{labelspath="";}
init(); refreshTiles(); checkTiles();}


function loadXMLfile(){ request = getHTTPObject(); request.onreadystatechange = xmlread; request.open("GET", path + "ImageProperties.xml", true); request.send(null);}


function xmlread() { if (request.readyState == 4) { var xmlDoc = request.responseXML.documentElement; width = parseFloat(xmlDoc.getAttribute("WIDTH")); height = parseFloat(xmlDoc.getAttribute("HEIGHT")); loadedXML=1; init();}
}


function stripPx(value) { if (value == ""){ return 0;}
return parseFloat(value.substring(0, value.length - 2));}


function winsize(){ viewportWidth = 1300; viewportHeight = 1000; if( typeof( window.innerWidth ) == 'number' ) { viewportWidth = window.innerWidth; viewportHeight = window.innerHeight;} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) { viewportWidth = document.documentElement.clientWidth; viewportHeight = document.documentElement.clientHeight;} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) { viewportWidth = document.body.clientWidth; viewportHeight = document.body.clientHeight;}
}


function $(i) { return document.getElementById(i);}


function handle(delta) { if (delta < 0){ if (wheelmode==0){ ZoomIn();}else{ slideNext();}
}else{ if (wheelmode==0){ ZoomOut();}else{ slidePrev();}
}
}


function wheel(event){ var delta = 0; if (!event){ event = window.event;}
if (event.wheelDelta) { delta = event.wheelDelta/120;} else if (event.detail) { delta = -event.detail/3;}
if (delta){ handle(delta);}
if (event.preventDefault){ event.preventDefault();}
event.returnValue = false;}
if (window.addEventListener){ window.addEventListener('DOMMouseScroll', wheel, false);}
window.onmousewheel = document.onmousewheel = wheel; 

function capturekey(e){ var k=(typeof event!='undefined')?window.event.keyCode:e.keyCode; if ( k == 187 || k== 61){ ZoomIn();}else if( k == 189 || k== 109){ ZoomOut();}else if( k == 39 || k== 40 || k== 34){ slideNext();}else if( k == 37 || k== 38 || k== 33){ slidePrev();}
}
if(navigator.appName!= "Mozilla"){document.onkeyup=capturekey}
else{document.addEventListener("keypress",capturekey,true);}


function getVar(name) { get_string = document.location.search; return_value = ''; do { name_index = get_string.indexOf(name + '='); if(name_index != -1) { get_string = get_string.substr(name_index + name.length + 1, get_string.length - name_index); end_of_value = get_string.indexOf('&'); if(end_of_value != -1)
value = get_string.substr(0, end_of_value); else
value = get_string; if(return_value == '' || value == '')
return_value += value; else
return_value += ', ' + value;}
} while(name_index != -1)
space = return_value.indexOf('+'); while(space != -1) { return_value = return_value.substr(0, space) + ' ' + return_value.substr(space + 1, return_value.length); space = return_value.indexOf('+');}
return(return_value);}


function getHTTPObject() { var xhr; try { xhr=new ActiveXObject("Msxml2.XMLHTTP");} catch (e){ try { xhr= new ActiveXObject("Microsoft.XMLHTTP");} catch (E){ xhr=false;}
}
if (!xhr && typeof XMLHttpRequest != ' undefined'){ xhr=new XMLHttpRequest();}
return xhr;}
