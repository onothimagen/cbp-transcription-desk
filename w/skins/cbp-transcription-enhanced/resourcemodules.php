<?php

/*
 * Definition of resources (CSS and Javascript) required for this skin.
 * This file must be included from LocalSettings.php since that is the only way
 * that this file is included by loader.php
 */
global $wgResourceModules, $wgStylePath, $wgStyleDirectory;

$wgResourceModules['skins.cbp-transcription-enhanced'] = array(
					'scripts' => 'cbp-transcription-enhanced/cbp-transcription-enhanced.js',
					'remoteBasePath' => &$GLOBALS['wgStylePath'],
					'localBasePath' => &$GLOBALS['wgStyleDirectory'],
);

?>