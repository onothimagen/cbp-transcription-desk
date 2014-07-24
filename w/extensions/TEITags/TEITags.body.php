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

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if ( !defined( 'MEDIAWIKI' ) ) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/TEITags/TEITags.php" );
EOT;
	exit( 1 );
}

class TEITagsHooks {

	public function ParserFirstCallInit ( Parser $parser ){
		global $wgOut;

		$parser->setHook( 'tei'    , array( $this , 'RenderTei' ) );
		$parser->setHook( 'lb'     , array( $this , 'RenderLb' ));
		$parser->setHook( 'pb'     , array( $this , 'RenderPb' ));
		$parser->setHook( 'del'    , array( $this , 'RenderDel' ));
		$parser->setHook( 'add'    , array( $this , 'RenderAdd' ));
		$parser->setHook( 'gap'    , array( $this , 'RenderGap' ));
		$parser->setHook( 'unclear', array( $this , 'RenderUnclear' ));
		$parser->setHook( 'note'   , array( $this , 'RenderNote' ));
		$parser->setHook( 'hi'     , array( $this , 'RenderHi' ));
		$parser->setHook( 'head'   , array( $this , 'RenderHead' ));
		$parser->setHook( 'sic'    , array( $this , 'RenderSic' ));
		$parser->setHook( 'foreign', array( $this , 'RenderForeign' ));

		$wgOut->addModules( 'ext.TEITags' );

		return true;

	}

	public function RenderTei(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'tei', $HookArgs );
	}

	public function RenderLb(){
		return '<br/>';
	}

	public function RenderPb(){
		return '<br/>---<em>page break</em>---<br/>';
	}

	public function RenderDel(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'del', $HookArgs );
	}

	public function RenderAdd(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'add', $HookArgs );
	}

	public function RenderGap(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'gap', $HookArgs );
	}

	public function RenderUnclear(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'unclear', $HookArgs );
	}

	public function RenderNote(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'note', $HookArgs );
	}

	public function RenderHi(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'hi', $HookArgs );
	}

	public function RenderHead(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'head', $HookArgs );
	}

	public function RenderSic(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'sic', $HookArgs );
	}

	public function RenderForeign(){
		$HookArgs = func_get_args();
		return $this->TEITagsRenderer( 'foreign', $HookArgs );
	}

	private function TEITagsRenderer ( $tag, $HookArgs ){

		$input  = $HookArgs[0];
		$args   = $HookArgs[1];
		$parser = $HookArgs[2];
		$frame  = $HookArgs[3];

		$output = '';

		if( $tag != 'gap' ){
			$output = $parser->recursiveTagParse( $input, $frame );
			$output = htmlspecialchars( $output );
		}

		if( $tag == 'hi' ){
			$render = $args['rend'];
			$tag 	.= ' ' . $render;
		}

		return '<span class="tei-' . $tag . '">' . $output . '</span>';
	}

}




















