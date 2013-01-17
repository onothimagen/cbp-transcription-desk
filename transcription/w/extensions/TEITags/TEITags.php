<?php

/**
 * Copyright (C) 2010 Richard Davis
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
 * @copyright 2010 Richard Davis
 */

if (!defined('MEDIAWIKI')) die();

$wgExtensionFunctions[] = "3";

$wgExtensionCredits['specialpage'][] = array(
					     'name'    => 'TEI-Tags',
					     'author'  => 'Richard Davis',
					     'version' => '0.1',
        'url'     => 'http://dablog.ulcc.ac.uk/'
					     );

/* TEI tags */
$wgExtensionFunctions[] = 'wfTEITags';
$wgExtensionCredits['parserhook'][] = array(
					    'name'    => 'TEI-Tags',
					    'author'  => 'Richard Davis',
					    'version' => '0.1',
        'url'     => 'http://dablog.ulcc.ac.uk/'
					    );

function wfTEITags() {
  global $wgParser;
  $wgParser->setHook( "tei", "wfRenderTEI" );
  $wgParser->setHook( "lb", "wfRenderLB" );
  $wgParser->setHook( "pb", "wfRenderPB" );
  $wgParser->setHook( "del", "wfRenderDEL" );
  $wgParser->setHook( "add", "wfRenderADD" );
  $wgParser->setHook( "gap", "wfRenderGAP" );
  $wgParser->setHook( "unclear", "wfRenderUNCLEAR" );
  $wgParser->setHook( "note", "wfRenderNOTE" );
  $wgParser->setHook( "hi", "wfRenderHI" );
  $wgParser->setHook( "head", "wfRenderHEAD" );
  $wgParser->setHook( "sic", "wfRenderSIC" );
  $wgParser->setHook( "foreign", "wfRenderFOREIGN" );
}

function wfRenderTEI( $input, $args, $parser, $frame ) {
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-tei'>" .
    htmlspecialchars( $output ) .
    "</span>";
}
function wfRenderLB() {
  return "<br/>";
}
function wfRenderPB() {
  return "<br/>---<em>page break</em>---<br/>";
}
function wfRenderDEL( $input, $args, $parser, $frame=FALSE ) {
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-del'>" .
    htmlspecialchars( $output ) .
    "</span>";
}
function wfRenderADD( $input, $args, $parser, $frame=FALSE ) {
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-add'>" .
    htmlspecialchars( $output ) .
    "</span>";
}
function wfRenderUNCLEAR( $input, $args, $parser, $frame=FALSE ) {
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-unclear'>" .
    htmlspecialchars( $output ) .
    "</span>";
}
function wfRenderNOTE( $input, $args, $parser, $frame=FALSE ) {
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-note'>" .
    htmlspecialchars( $output ) .
    "</span>";
}

function wfRenderHI( $input, $args, $parser, $frame=FALSE ) {
  $render = $args['rend'];
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-hi $render'>" .
    htmlspecialchars( $output ) .
    "</span>";
}

function wfRenderHEAD( $input, $args, $parser, $frame=FALSE ) {
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-head'>" .
    htmlspecialchars( $output ) .
    "</span>";
}

function wfRenderFOREIGN( $input, $args, $parser, $frame=FALSE ) {
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-foreign'>" .
    htmlspecialchars( $output ) .
    "</span>";
}

function wfRenderSIC( $input, $args, $parser, $frame=FALSE ) {
  $output = $parser->recursiveTagParse( $input, $frame );
  return "<span class='tei-sic'>" .
    htmlspecialchars( $output ) .
    "</span>";
}

function wfRenderGAP() {
  return "<span class='tei-gap'>" .
    "&nbsp;" .
    "</span>";
}

/* show list of tags  */
function wfTEITagsSpecialpage() {

  class TEITags extends SpecialPage {

    function TEITags() {
      SpecialPage::SpecialPage( 'TEITags' );
      $this->includable(true);
      $this->listed(false);
    }

    function execute( $subPage ) {
      global $wgOut, $wgRequest;


      $text = "some text";

      exit;
    }
  } /* class */
  SpecialPage::addPage( new TEITags );
}
