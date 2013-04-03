<?php

namespace Classes\Exceptions;

class Importer extends \Exception {


	protected $aBacktrace;

	public function __construct( $message = null, $code = 0 ) {
		parent::__construct( $message, $code );
		$this->aBacktrace = debug_backtrace();
	}

	public function getFullTrace() {
		return strip_tags( $this->backtrace( $this->aBacktrace ) );
	}

	public function backtrace($aBackTrace=false){
		$output = "<div style='text-align: left; font-family: monospace;'>\n";
		$output .= "<b>Backtrace:</b><br />\n";
		if ($aBackTrace) {
			$backtrace = $aBackTrace;
		} else {
			$backtrace = debug_backtrace();
		}
		foreach ($backtrace as $bt) {
			$args = '';
			foreach ( $bt['args'] as $a ) {
				if (!empty( $args )) {
					$args .= ', ';
				}
				switch (gettype( $a )) {
					case 'integer':
					case 'double':
						$args .= $a;
						break;
					case 'string':
						$a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
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
			if( isset( $bt['line'] ) && isset( $bt['file'] ) ){
				$output .= "<br />\n";
				$output .= "<b>file:</b> {$bt['line']} - {$bt['file']}<br />\n";
				$sClass    = isset( $bt['class'] )    ? $bt['class']    : '' ;
				$sType     = isset( $bt['type'] )     ? $bt['type']     : '' ;
				$sFunction = isset( $bt['function'] ) ? $bt['function'] : '' ;
				$output .= "<b>call:</b> {$sClass}{$sType}{$sFunction}($args)<br />\n";
			}
		}
		$output .= "</div>\n";
		return $output;
	}



}
