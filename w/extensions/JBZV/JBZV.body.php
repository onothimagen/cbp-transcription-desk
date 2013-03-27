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

class JBZVHooks{

	private $titleOptions;
	private $imagesRootDir;
	private $imagePath;
	private $mediaWikiDir;
	private $lang;

	private $pageTitle;
	private $ImageName;

	private $viewerType;
	private $extractedTokens;

	/**
	 * Assigns globals to properties
	 * Creates default values when these have not been set
	 */
	private function assignGlobalsToProperties(){

		global $wgJBZVTitleOptions, $wgScriptPath, $wgJBZVimagesRootDir, $wgLang;

		$imagesRootDir = $wgJBZVimagesRootDir;

		if( isset( $imagesRootDir ) === false ){
			$imagesRootDir = $wgScriptPath . '/zimages';
		}

		if( isset( $wgJBZVTitleOptions ) === false ){
			$wgJBZVTitleOptions = array(
										  'site_name'		    => 'Transcribe Bentham'
										, 'regx_pattern'        => '^[.*](\d\d\d)\/(\d\d\d)\/(\d\d\d).*$'
										, 'dir_token_indexes'   => array( 1)
										, 'name_token_indexes'  => array( 1, 2, 3 )
										, 'file_name_seperator' => '_'
								);
		}

		$this->titleOptions  = $wgJBZVTitleOptions;
		$this->imagesRootDir = $imagesRootDir;
		$this->mediaWikiDir  = $wgScriptPath;
		$this->lang			 = $wgLang->getCode();

		return;

	}

	/**
	 * editPageShowEditFormFields hook
	 *
	 * @param $editPage EditPage
	 * @param $output OutputPage
	 * @return bool
	 */

	public function onEditPageShowEditFormInitial( EditPage $editPage, OutputPage &$output ) {

		if( isset( $_GET[ 'action' ] ) and $_GET[ 'action' ] !== 'edit' ){
			return true;
		}

		$this->assignGlobalsToProperties();

		$this->loadViewer( $output );

		return true;
	}

	/*
	 * @param $article Article
	 * @param $row
	 * @return bool
	 */

	public function onArticlePageDataAfter( WikiPage $article, $row ){
		global $wgOut;

		if( isset( $_GET[ 'action' ] ) and $_GET[ 'action' ] === 'edit' ){
			return true;
		}

		$this->assignGlobalsToProperties();

		$output = $wgOut;

		$this->loadViewer( $output );

		return true;
	}

	/**
	 * Adds the iframe HTML to the page
	 *
	 * @param $output OutputPage
	 * @return bool
	 */

	private function loadViewer( OutputPage $output ){

		$pageTitle       = $output->GetTitle();
		$this->pageTitle = $pageTitle;

		if( $this->isInViewerMode( $this->pageTitle ) === false ){
			return true;
		}

		$output->addModules( 'ext.JBZV' );

		$viewContent      = $this->formatIframeHTML();

		$output->addHTML( $viewContent );


	}

	/**
	 * Generates the HTML for the iframe
	 *
	 * @return string
	 */

	private function formatIframeHTML(){

		$mediaWikiDir  = $this->mediaWikiDir;

		$viewerPath    = $this->getViewerPath();

		$imageFilePath = $this->constructImageFilePath();

		$lang		   = $this->lang;

		$siteName	   = $this->titleOptions[ 'site_name' ];

		$iframeHTML = '<iframe id="zoomviewerframe" src="' .  $mediaWikiDir . '/extensions/JBZV/' . $viewerPath . '?image=' . $imageFilePath . '&amp;lang=' . $lang . '&amp;sitename=' . urlencode( $siteName ) . '"></iframe>';

		return $iframeHTML;

	}

	/**
	 * Returns the relative path to the viewer
	 *
	 * @return string
	*/

	private function getViewerPath(){

		$viewerType = $this->getViewerType();

		if( $viewerType == 'js' ){
			return 'tools/ajax-tiledviewer/ajax-tiledviewer.php';
		}

		return 'tools/zoomify/zoomifyviewer.php';

	}

	/**
	 * Gets the default viewer type
	 *
	 * @return string
	*/

	private function getViewerType(){

		if( $this->viewerType !== NULL){
			return $this->viewerType;
		}

		if ( $this->browserIsIE() ) {
			return 'js';
		}

		return 'zv';

	}

	/**
	 * Constructs the full path of the image to be passed to the iframe
	 *
	 *  It then
	 *
	 * @return string
	*/

	private function constructImageFilePath(){

		$collectionDir   = $this->constructCollectionDirectory();

		$imageName       = $this->constructImageName();

		$imagesRootDir   = $this->imagesRootDir;

		$ImageFilePath   = $imagesRootDir . '/' . $collectionDir. '/' . $imageName. '/';

		return $ImageFilePath;
	}

	/**
	 * Extracts the tokens from the page title based on the supplied regular expression.
	 *
     * Example:
     *
     * Set the pattern in LocalSettings.php
     * $wgJBZVTitleOptions['regx_pattern'] = '^JB\/(\d\d\d)\/(\d\d\d)\/(\d\d\d)^'
     *
	 * Extracts 002, 006, 001
	 * from 'JB/002/006/001 - Transcribe Bentham: Transcription Desk'
	 *
	 * @return string
	 */

	private function extractTokensFromTitle(){

		if( $this->extractedTokens !== NULL){
			return $this->extractedTokens;
		}

		$ImagePath  = $this->imagePath;

		$pattern   = $this->titleOptions[ 'regx_pattern'];

		$pageTitle = $this->pageTitle;



		preg_match( $pattern, $pageTitle, $extractedTokens );

		$this->extractedTokens = $extractedTokens;

		return $extractedTokens;

	}


	/**
	 * Constructs the image collection's directory
	 *
	 * The path folders are constructed from the tokens ( defined by titleOptions[ 'dir_token_indexes' ] )
	 * extracted from the page title
	 *
	 * @return string
	*/

	private function constructCollectionDirectory(){

		$extractedTokens = $this->extractTokensFromTitle();

		$dirTokenIndexes  = $this->titleOptions[ 'dir_token_indexes' ];

		$ImageDir = '';

		foreach ( $dirTokenIndexes as $index => $tokenIndex ){
			$ImageDir .= $extractedTokens[ $tokenIndex ];
		}

		return $ImageDir;

	}

	/**
	 * Constructs the image name
	 *
	 * It gets the tokens extracted from the page title
	 * and creates a name using the concatenator
	 *
	 * @return string
	*/

	private function constructImageName( ){

		$extractedTokens = $this->extractTokensFromTitle();

		$nameTokenIndexes = $this->titleOptions[ 'name_token_indexes' ];
		$seperator        = $this->titleOptions[ 'file_name_seperator' ];

		$ImageName = '';

		foreach ( $nameTokenIndexes as $index => $token_index ){

			$ImageName .= $extractedTokens[ $token_index ];

			if( $index < count( $nameTokenIndexes ) -1 ){
				$ImageName .= $seperator;
			}
		}

		return $ImageName;
	}

	/**
	 * Determines whether the page should display the viewer by
	 * parsing the page's titles using the regular expression
	 * defined in titleOptions[ 'regx_pattern' ]
	 *
	 * @return bool
	*/

	private function isInViewerMode(){

		$pageTitle = $this->pageTitle;
		$regX      = $this->titleOptions[ 'regx_pattern' ];

		if ( preg_match( $regX, $pageTitle ) ){
			return TRUE;
		}

		return FALSE;

	}

	/**
	 * Determines whether the browser is Internet Explorer
	 *
	 * @return bool
	 */

	private function browserIsIE(){

		$userAgent = $_SERVER[ 'HTTP_USER_AGENT' ];

		if( preg_match( '/MSIE/i', $userAgent ) ){
			return TRUE;
		}

		return FALSE;

	}



}































