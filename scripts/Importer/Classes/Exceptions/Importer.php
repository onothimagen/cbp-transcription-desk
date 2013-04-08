<?php

/**
 * Copyright (C) Based on comment from diz@ysagoon.com on http://php.net/manual/en/function.debug-backtrace.php

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
 * @author diz@ysagoon.com
 * @copyright Based on comment from diz@ysagoon.com on http://php.net/manual/en/function.debug-backtrace.php
 */

namespace Classes\Exceptions;

class Importer extends \Exception {


	protected $aBacktrace;

	public function __construct( $message = null, $code = 0 ) {
		parent::__construct( $message, $code );
		$this->aBacktrace = debug_backtrace();
	}

	public function getFullTrace() {
		return strip_tags( $this->Backtrace( $this->aBacktrace ) );
	}

	public function Backtrace($aBackTrace=false){
		$output = '<div style=\'text-align: left; font-family: monospace;\'>' . "\n";
		$output .= '<b>Backtrace:</b><br />'."\n";
		if ($aBackTrace) {
			$backtraces = $aBackTrace;
		} else {
			$backtraces = debug_backtrace();
		}
		foreach ( $backtraces as $backtrace ) {
			$args = '';
			foreach ( $backtrace['args'] as $a ) {
				if (!empty( $args )) {
					$args .= ', ';
				}
				switch (gettype( $a )) {
					case 'integer':
					case 'double':
						$args .= $a;
						break;
					case 'string':
						$a = htmlspecialchars(substr( $a, 0, 64 )).( ( strlen($a) > 64 ) ? '...' : '' );
						$args .= "\"$a\"";
						break;
					case 'array':
						$args .= 'Array('.count($a).')';
						break;
					case 'object':
						$args .= 'Object('.get_class($a).')';
						break;
					case 'resource':
						$args .= 'Resource('.strstr($a, '#').')';
						break;
					case 'boolean':
						$args .= $a ? 'True' : 'False';
						break;
					case 'NULL':
						$args .= 'Null';
						break;
					default:
						$args .= 'Unknown';
				}
			}
			if( isset( $backtrace['line'] ) && isset( $backtrace['file'] ) ){
				$output .= "<br />\n";
				$output .= "<b>file:</b> {$backtrace['line']} - {$backtrace['file']}<br />\n";
				$sClass    = isset( $backtrace['class'] )    ? $backtrace['class']    : '' ;
				$sType     = isset( $backtrace['type'] )     ? $backtrace['type']     : '' ;
				$sFunction = isset( $backtrace['function'] ) ? $backtrace['function'] : '' ;
				$output .= "<b>call:</b> {$sClass}{$sType}{$sFunction}($args)<br />\n";
			}
		}
		$output .= "</div>\n";
		return $output;
	}



}
