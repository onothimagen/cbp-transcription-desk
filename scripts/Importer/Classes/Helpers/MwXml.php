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
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013  University College London
 */


namespace Classes\Helpers;

use Classes\Entities\Folio as FolioEntity;


class MwXml{

	private $sPagePrefix;

	private $sVersion;
	private $sExportVersion;

	private $sWebHost;
	private $sSiteName;
	private $sHomePageName;
	private $sPagePath;

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

	public function __construct( array $aSectionConfig ){

		$this->sPagePrefix    = $aSectionConfig[ 'page_prefix' ];


		$this->sVersion       = $aSectionConfig[ 'version' ];
		$this->sExportVersion = $aSectionConfig[ 'export_version' ];

		$this->sWebHost       = $aSectionConfig[ 'webhost' ];
		$this->sSiteName      = $aSectionConfig[ 'site_name' ];
		$this->sHomePageName  = $aSectionConfig[ 'home_page_name' ];
		$this->sPagePath      = $aSectionConfig[ 'page_path' ];

	}

	/*
	 * @return DOMDocument
	 */
	public function InitialiseDocument(){

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

		$sBase = 'http://' . $this->sWebHost . '/' . $this->sPagePath . '/' . $this->sHomePageName;

		// This is a hack because it does not like  $oDomDocument->createElement( 'base' );

		$oElement            = $oDomDocument->createElement('base');

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

	/*
	 * @return DOMDocument
	 */
	public function CreatePageElement(  FolioEntity $oEntity
									  , \DOMDocument   $oDomDocument
									  ,                $sText ){

		$oRootNodes   = $oDomDocument->getElementsByTagName( 'mediawiki' );

		$oRootNode    = $oRootNodes->item(0);

		$oPageElement = $oDomDocument->createElement( 'page' );

		$oRootNode->appendChild( $oPageElement );



		$oElement  = $oDomDocument->createElement( 'title' );

		$sItemPath = $this->CreateItemPath( $oEntity );

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
	 *
	 */
	public function CreateFolioText(
										  FolioEntity $oNextEntity
										, FolioEntity $oEntity
										, FolioEntity $oPrevEntity
										){

		$sIdentifier = $this->CreateItemPath( $oEntity );
		$sNext       = $this->CreateItemPath( $oNextEntity );
		$sPrev       = $this->CreateItemPath( $oPrevEntity );

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
	 *
	 */
	public function CreatePageText( FolioEntity $oEntity ){

		$sItemPath = $this->CreateItemPath( $oEntity );

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
	 *
	 */
	private function CreateItemPath( FolioEntity $oEntity ){

		$sPrefix      = $this->sPagePrefix;

		$sBoxNumber   = $oEntity->getBoxNumber();
		$sFolioNumber = $oEntity->getFolioNumber();
		$sItemNumber  = $oEntity->getItemNumber();

		$sItemPath    = $sPrefix . '/' . $sBoxNumber . '/' . $sFolioNumber . '/' . $sItemNumber;

		return $sItemPath;

	}

}













































