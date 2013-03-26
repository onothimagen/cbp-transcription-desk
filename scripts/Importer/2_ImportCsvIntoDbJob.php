<?php

namespace Classes;

if( isset ($_SERVER[ 'SCRIPT_FILENAME' ]) ){

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="en"
    lang="en"
    dir="ltr">
<head>
<link rel="icon" href="./favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
<title>Import CSV Into DB</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

}

require_once 'global.inc.php';

$sCsvFilePath                   = $aData[ $sConfigSection ]['common']['csvfilepath'];

$oCsvRowToMetatDataEntityMapper = new Mappers\CsvRowToMetaDataEntity();

$oImportCsvIntoDbTask           = new ImportCsvIntoDbTask( $oMetaDataDb
														 , $oItemDb
														 , $oFileHelper
											             , $oCsvRowToMetatDataEntityMapper
														 , $oLogger
														 , $sCsvFilePath
														 , $iJoBQueueId );

try {
	$oImportCsvIntoDbTask->Execute();
} catch ( Exception $e ) {
	// Write to log
}

/* Import of the MetaData and Items was successfuk so start slicing the images */

require '3_SliceImagesJob.php';


if( isset ($_SERVER[ 'SCRIPT_FILENAME' ]) ){
	?>
</body>
</html>
<?php

}

?>

