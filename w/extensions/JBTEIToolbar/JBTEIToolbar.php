<?
 
if( !isset($wgJBTEIToolbarPath) ) {
  $wgJBTEIToolbarPath = "$wgScriptPath/extensions/JBTEIToolbar";
}
 
function addJBTEIButtons($text) {
  global $wgStylePath;
  global $wgJBTEIToolbarPath;
  $arr_tool = array(
		    array(  'image' =>'button_save.png',
			    'tip'   => wfMsg('savearticle'),
			    'button'=>"wpSave",
			    ),
		    array(  'image' =>'button_preview.png',
			    'tip'   => wfMsg('showpreview'),
			    'button'=>"wpPreview",
			    ),
		    array(  'image' =>'button_diff.png',
			    'tip'   => wfMsg('showdiff'),
			    'button'=>"wpDiff",
			    ),
		    );
 
  $ret = addJBTEIButtons_js_text();
 
  foreach($arr_tool as $tool) {
    $image=$wgJBTEIToolbarPath ."/images/" . $tool['image'];
    $button=$tool['button'];
    $tip=$tool['tip'];
 
    $ret.="addJBTEIButtons('$image','$tip','$button');\n";
  }
  $text.=$ret;
  return true;
}
 
function addJBTEIButtons_js_text() {
        return <<<EOL
	  ///////////////////////////////////////////////////////////////////////
	  // Submit buttons extension
	  //
	  var mwJBTEIButtons = [];
 
	function addJBTEIButtons(imageFile, speedTip, buttonId) {
        mwJBTEIButtons[mwJBTEIButtons.length] =
	  {"imageFile": imageFile,
	   "speedTip": speedTip,
	   "buttonId": buttonId };
	}
 
	function addJBTEIButtons_insert(toolbar, item){
 
	  var image = document.createElement("img");
	  image.width = 23;
	  image.height = 22;
	  image.src = item.imageFile;
	  image.border = 0;
	  image.alt = item.speedTip;
	  image.title = item.speedTip;
	  image.style.cursor = "pointer";
	  var buttonId= item.buttonId;
	  image.onclick = function() {
	    var button =  document.getElementById(buttonId);
	    button.click();
	    return false;
	  }
	  //              image.onclick = 'javascript:document.getElementById("' +
	  //                              escapeQuotesHTML(buttonId) +
	  //                               '").click();';
	  toolbar.appendChild(image);
	}
 
	function addJBTEIButtons() {
 
	  var toolbar = document.getElementById('toolbar');
	  if (!toolbar) return false;
	  sp = document.createElement('span');
        sp.innerHTML= "&nbsp;"
	  toolbar.appendChild(sp);
 
        for(var i in mwJBTEIButtons) {
	  addJBTEIButtons_insert(toolbar, mwJBTEIButtons[i]);
 
        }
	}
 
	hookEvent("load", addJBTEIButtons);
 
EOL;
}
 
/*
        document.write(
                '<a href="javascript:' + 
                        '(document.getElementById(' +
                                "'" + escapeQuotesHTML(buttonId) + "'" +
                        ')).click()"' +
                '>' +
                '<img width="23" height="22" ' +
                        'src="' + escapeQuotesHTML(imageFile) + '" ' +
                        'border="0" ' +
                        'alt="' + escapeQuotesHTML(speedTip) + '" ' +
                        'title="'+ escapeQuotesHTML(speedTip) + '" ' +
                '/></a>');
 
}
 
*/
$wgHooks['EditToolBar'][] = 'addJBTEIButtons';
?>
