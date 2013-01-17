<?php
 
if ( ! defined( 'MEDIAWIKI' ) )
        die();
 
//---------- Note Author--- 08-2010-------------------
// The JBZV Transcription Editor is designed to add an 
// iframe next to the edit form so that it can be 
// transcribed using the edit box.
//----------------------------------------------------
 
$wgExtensionCredits['other'][] = array(
'name' => 'JBZVTranscriptionEditor',
'author' => 'Richard Davis',
'url' => 'http://www.transcribe-bentham.da.ulcc.ac.uk',
'version' => '0.1',
'description' => 'Extension to add an image to the edit page for transcription purposes',
);
 

//####### Used Hooks ######################

$wgHooks['EditPage::showEditForm:fields'][] = 'JBImageDisplay';

 
########## Hook 1 #################
function JBImageDisplay(&$q, &$out) {

  $pagetitle = $out->getPageTitle();

  if (   preg_match("/^Editing JB\//",$pagetitle )
      or preg_match("/^View source/",$pagetitle) ) {
    $out->addScript('<style type="text/css">#wpTextbox1 { width: 38%; min-height: 660px; }</style>');

    $item = $pagetitle;
    $item = preg_replace("/^Editing JB\//","", $item);
    $item = preg_replace("/\//","_", $item);
        
    $out->addHTML("<iframe id='myiframe' style='float: right; width: 60%; height: 660px;' src='/td/extensions/JBZV/zviewer.php?item=$item' scrolling='no'></iframe>");
  } 
  
return true;
 
}

