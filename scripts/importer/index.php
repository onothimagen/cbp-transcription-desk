<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<link rel="icon" href="./favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
<title>Run Jobs</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

// NOTE: This is intended to be called using Apache CGI and not PHP CLI
// You will get errors if run from PHP CLI

// Disabling output buffering is required for using the system() command for real time output

function disable_ob() {
    // Turn off output buffering
    ini_set('output_buffering', 'off');
    // Turn off PHP output compression
    ini_set('zlib.output_compression', false);
    // Implicitly flush the buffer(s)
    ini_set('implicit_flush', true);
    ob_implicit_flush(true);
    // Clear, and turn off output buffering
    while (ob_get_level() > 0) {
        // Get the curent level
        $level = ob_get_level();
        // End the buffering
        ob_end_clean();
        // If the current level has not changed, abort
        if (ob_get_level() == $level) break;
    }
    // Disable apache output buffering/compression
    if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', '1');
        apache_setenv('dont-vary', '1');
    }
}

function GetId(){

	if( isset( $_GET[ 'job_id' ] ) ){
		$iJobId = (int) $_GET[ 'job_id' ];
		return ' ' . $iJobId;
	}elseif ( isset( $argv[ 1 ] ) ){
		$iJobId = (int) $argv[ 1 ];
		return ' ' . $iJobId;
	}
	return '';
}

disable_ob();

$iJobId = GetId();

// Ensure that the php binary is added to the system path

$sCommand = 'php 1_InitiateJobs.php' . $iJobId;

passthru( $sCommand );

?>
</body>
</html>