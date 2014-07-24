;<?php
;die(); // For further security
;?>
;/*

[common]
active.section	     = development
mediawiki.version    = 1.23.1
export.version       = 0.7
database.adapter     = Pdo_Mysql
database.params.host = localhost
file.group           = webgroup
site.name            = Transcription Desk
homepage.name       = Transcribe
page.path            = td;
page.prefix          = TD;

;For example  BOX_001/001_001_001.jpg

box.prefix           = BOX_
tokenseperator       = _
regex.box            = \d\d\d
regex.folio			 = \w+
regex.item           = \d\d\d[rv]?_?\d?

import_box_limit	 = 1

path.image.export    = zimages
path.mw.importer     = w/maintenance/importDump.php
path.slicer          = scripts/Slicer/slicer.pl
path.csv.import      = cbp-data/import/metatada-table.txt
path.image.import    = cbp-data/import/images
path.xml.export      = cbp-data/import/xml
path.logs            = cbp-data/logs
path.archive         = cbp-data/archive

[production : common]

[staging : common]

[development : common]
host        = cbp-transcription-desk-test.local
admin.email =
path.prefix = M:/web/sites/cbp-transcription-desk-test.local/

database.params.username = transcription
database.params.password = transcription
database.params.dbname   = transcription


;*/
