<?php

/**
 * Copyright (C) University College London
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
 * @package CBP Transcription
 * @subpackage Importer
 * @version 1.0
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  University College London
 */


namespace Classes\Mappers;

use Classes\Entities\Folio as FolioItemEntity;

use Classes\Db\MediaWiki as MediaWikiDb;

use Zend\Di\Di;

/*
 * Builds an XML DOM document
 *
 */
class JobItemsToMwXml{

	/* @var MediaWikiDb */
	private $oMediaWikiDb;

	private $sPagePrefix;

	private $sVersion;
	private $sExportVersion;

	private $sHost;
	private $sSiteName;
	private $sHomePageName;
	private $sPagePath;

	/* @var DOMDocument */
	private $oDocument;

	private $oNextEntity;
	private $oEntity;
	private $oPrevEntity;

	private $aNameSpaces = array(
							  '-2'  => 'Media'
							, '-1'  => 'Special'
							, '0'   => ''
							, '1'   => 'Talk'
							, '2'   => 'User'
							, '3'   => 'User talk'
							, '4'   => 'Transcribe Bentham Transcription Desk'
							, '5'   => 'Transcribe Bentham Transcription Desk talk'
							, '6'   => 'File'
							, '7'   => 'File talk'
							, '8'   => 'MediaWiki'
							, '9'   => 'MediaWiki talk'
							, '10'  => 'Template'
							, '11'  => 'Template talk'
							, '12'  => 'Help'
							, '13'  => 'Help talk'
							, '14'  => 'Category'
							, '15'  => 'Category talk'
							, '100' => 'Metadata'
							, '101' => 'Metadata talk'
							, '102' => 'Property'
							, '103' => 'Property talk'
							, '104' => 'Type'
							, '105' => 'Type talk'
							, '108' => 'Concept'
							, '109' => 'Concept talk'
							, '200' => 'UserWiki'
							, '201' => 'UserWiki talk'
							, '202' => 'User profile'
							, '203' => 'User profile talk'
							, '121707' => 'Forum'
							);

	/*
	 * Creates a blank DOM document and creates the XML container ready for populating
	 *
	 * @return void
	 */
	public function __construct( MediaWikiDb $oMediaWikiDb, array $aConfig ){

		/* @var $oMediaWikiDb MediaWikiDb */
		$this->oMediaWikiDb   = $oMediaWikiDb;

		$this->sPagePrefix    = $aConfig[ 'page.prefix' ];


		$this->sVersion       = $aConfig[ 'mediawiki.version' ];
		$this->sExportVersion = $aConfig[ 'export.version' ];

		$this->sHost          = $aConfig[ 'host' ];
		$this->sSiteName      = $aConfig[ 'site.name' ];
		$this->sHomePageName  = $aConfig[ 'homepage.name' ];
		$this->sPagePath      = $aConfig[ 'page.path' ];

		$this->oDocument      = $this->InitialiseDocument();

	}




   /*
	* Creates and metadata text and default page text
	* Appends these to the DOM Document
	*
	* @param  FolioItemEntity $oNextEntity
	* @param  FolioItemEntity $oEntity
	* @param  FolioItemEntity $oPrevEntity
	*/
	public function CreateItemPages(  FolioItemEntity $oNextEntity
									, FolioItemEntity $oEntity
									, FolioItemEntity $oPrevEntity ){

		$this->oNextEntity = $oNextEntity;
		$this->oEntity     = $oEntity;
		$this->oPrevEntity = $oPrevEntity;

		// Create MetaData Text and append it to DOM document

		$sMetaDataText  = $this->CreateItemsMetaDataText();

		$oFolioTextNode = $this->CreateItemPageElement( $sMetaDataText );

		// Create Page Text and append it to DOM document

		$oPageText      = $this->CreateDefaultPageText();

		$oPageTextNode  = $this->CreateItemPageElement( $oPageText );

	}

	public function GetDocument(){
		return $this->oDocument;
	}


	/*
	 * Creates a series XML elements populated with default values
	 * and the text passed in which will be either
	 * be the meta data or page's default text
	 *
	 * @param  string
	 * @return DOMDocument
	 */
	private function CreateItemPageElement( $sText ){

		$oEntity      = $this->oEntity;
		$oDomDocument = $this->oDocument;

		$sItemPath = $this->CreateItemPath( $oEntity );

		/* Do not add pages that already exist in MediaWiki */
		if( $this->oMediaWikiDb->DoesItemPageExist( $sItemPath ) ){
			return $oDomDocument;
		}

		if( strpos( $sText, '{{Infobox Folio New' ) !== false ){
			$sItemPath = 'Metadata:' . $sItemPath;
		}


		$oRootNodes   = $oDomDocument->getElementsByTagName( 'mediawiki' );

		$oRootNode    = $oRootNodes->item(0);

		$oPageElement = $oDomDocument->createElement( 'page' );

		$oRootNode->appendChild( $oPageElement );



		$oElement  = $oDomDocument->createElement( 'title' );

		$oElement->nodeValue = $sItemPath;

		$oPageElement->appendChild( $oElement );



		$oRevisionElement    = $oDomDocument->createElement( 'revision' );

		$oPageElement->appendChild( $oRevisionElement );



		$oContributorElement    = $oDomDocument->createElement( 'contributor' );

		$oRevisionElement->appendChild( $oContributorElement );



		$oElement    = $oDomDocument->createElement( 'username' );

		$oElement->nodeValue = 'BenthamBot';

		$oContributorElement->appendChild( $oElement );



		$oElement    = $oDomDocument->createElement( 'comment' );

		$oElement->nodeValue = 'Auto upload';

		$oRevisionElement->appendChild( $oElement );


		$oElement    = $oDomDocument->createElement( 'text' );

		$oElement->nodeValue = $sText;

		$oElement->setAttribute( 'xml:space', 'preserve' );

		$oRevisionElement->appendChild( $oElement );

		return $oDomDocument;


	}



	/*
	 * Creates a pipe seperated key - value pair list
	 *
	 * @return string
	 */
	private function CreateItemsMetaDataText(){

		$oEntity     = $this->oEntity;

		$sIdentifier = $this->CreateItemPath( $oEntity );
		$sNext       = $this->CreateItemPath( $this->oNextEntity );
		$sPrev       = $this->CreateItemPath( $this->oPrevEntity );


		$sText       = '{{Infobox Folio New' . "\n";

		$aFields = array(
						  'box_number'                  => $oEntity->getBoxNumber()
						, 'folio_number'                => $oEntity->getFolioNumber()
						, 'second_folio_number'         => $oEntity->getSecondFolioNumber()
						, 'category'                    => $oEntity->getCategory()
						, 'rectoverso'                  => $oEntity->getRectoVerso()
						, 'creator'                     => $oEntity->getCreator()
						, 'recipient'                   => $oEntity->getRecipient()
						, 'penner'                      => $oEntity->getPenner()
						, 'marginals'                   => $oEntity->getMarginals()
						, 'corrections'                 => $oEntity->getCorrections()
						, 'date_1'                      => $oEntity->getDate_1()
						, 'date_2'                      => $oEntity->getDate_2()
						, 'date_3'                      => $oEntity->getDate_3()
						, 'date_4'                      => $oEntity->getDate_4()
						, 'date_5'                      => $oEntity->getDate_5()
						, 'date_6'                      => $oEntity->getDate_6()
						, 'estimated_date'              => $oEntity->getEstimatedDate()
						, 'info_in_main_headings_field' => $oEntity->getInfoInMainHeadingField()
						, 'main_headings'               => $oEntity->getMainHeading()
						, 'sub_headings'                => $oEntity->getSubHeadings()
						, 'marginal_summary_numbering'  => $oEntity->getMarginalSummaryNumbering()
						, 'number_of_pages'             => $oEntity->getNumberOfPages()
						, 'page_numbering'              => $oEntity->getPageNumbering()
						, 'titles'                      => $oEntity->getTitles()
						, 'watermarks'                  => $oEntity->getWatermarks()
						, 'paper_producer'              => $oEntity->getPaperProducer()
						, 'paper_produced_in_year'      => $oEntity->getPaperProducerInYear()
						, 'notes_public'                => $oEntity->getNotesPublic()
						, 'id_number'                   => $oEntity->getItemNumber()
						, 'image_number'                => $oEntity->getItemNumber()
						, 'identifier'                  => $sIdentifier
						, 'next'                        => $sNext
						, 'prev'                        => $sPrev

						);

		foreach ( $aFields as $sKey => $sValue ){

			$sText .= '| ' . $sKey . ' = ' . $sValue . "\n";
		}

		$sText .= '}}';

		return htmlspecialchars( $sText );

	}

	/*
	 * @return string
	 */
	private function CreateDefaultPageText(){

		$sItemPath = $this->CreateItemPath( $this->oEntity );

		$sText     = <<<TEXTBLOCK
'[{{fullurl:$sItemPath|action=edit}} Click Here To Edit]'''
&lt;!-- ENTER TRANSCRIPTION BELOW THIS LINE --&gt;

''This Page Has Not Been Transcribed Yet''



&lt;!-- DO NOT EDIT BELOW THIS LINE --&gt;
{{Metadata:{{PAGENAME}}}}
TEXTBLOCK;
		return $sText;
	}



	/*
	 * Provide a path for the page text edit link
	 * e.g. JB/101/234/001
	 *
	 * @return string
	 */
	private function CreateItemPath( FolioItemEntity $oEntity ){

		$sPrefix      = $this->sPagePrefix;

		$sBoxNumber   = $oEntity->getBoxNumber();
		$sFolioNumber = $oEntity->getFolioNumber();
		$sItemNumber  = $oEntity->getItemNumber();

		$sItemPath    = $sPrefix . '/' . $sBoxNumber . '/' . $sFolioNumber . '/' . $sItemNumber;

		return $sItemPath;

	}



	/*
	 * Pre-populate DOm document with default XML
	 *
	 * @return DOMDocument
	*/
	private function InitialiseDocument(){

		$oDomDocument = new \DOMDocument( '1.0', 'utf-8' );

		$sNameSpace   = 'http://www.mediawiki.org/xml/export-' . $this->sExportVersion . '/';

		$oRootNode = $oDomDocument->createElementNS( $sNameSpace, 'mediawiki' );

		$oDomDocument->appendChild( $oRootNode );

		$oRootNode->setAttributeNS( 'http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance' );

		$oRootNode->setAttribute( 'xsi:schemaLocation', $sNameSpace . ' http://www.mediawiki.org/xml/export-' . $this->sExportVersion . '.xsd');

		$oRootNode->setAttribute( 'version', $this->sExportVersion );

		$oRootNode->setAttribute( 'xml:lang', 'en');



		$sSiteInfoElement    = $oDomDocument->createElement( 'siteinfo' );

		$oRootNode->appendChild( $sSiteInfoElement );



		$oElement            = $oDomDocument->createElement( 'sitename' );

		$oElement->nodeValue = $this->sSiteName;

		$sSiteInfoElement->appendChild( $oElement );



		$oElement            = $oDomDocument->createElement( 'base' );

		$sBase = 'http://' . $this->sHost . '/' . $this->sPagePath . '/' . $this->sHomePageName;

		$oElement->nodeValue = $sBase;


		$sSiteInfoElement->appendChild( $oElement );



		$oElement            = $oDomDocument->createElement( 'generator' );

		$oElement->nodeValue = 'MediaWiki ' . $this->sVersion;

		$sSiteInfoElement->appendChild( $oElement );



		$oElement            = $oDomDocument->createElement( 'case' );

		$oElement->nodeValue = 'first-letter';

		$sSiteInfoElement->appendChild( $oElement );


		$sNameSpaceElement = $oDomDocument->createElement( 'namespaces' );

		$sSiteInfoElement->appendChild( $sNameSpaceElement );


		foreach ( $this->aNameSpaces as $key => $value ){

			$oElement = $oDomDocument->createElement( 'namespace' );

			$oElement->nodeValue = $value;

			$oElement->setAttribute( 'key', $key );

			$oElement->setAttribute( 'case', 'first-letter' );

			$sNameSpaceElement->appendChild( $oElement );

		}


		return $oDomDocument;


	}



}













































