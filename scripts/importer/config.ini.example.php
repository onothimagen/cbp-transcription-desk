;<?php
;die(); // For further security
;/*

[common]
active.environment	 = development
mediawiki.version    = 1.20.2
export.version       = 0.7
database.adapter     = Pdo_Mysql
database.params.host = localhost
site_name            = 'Transcription Desk'
home_page_name       = 'Transcribe'
box.prefix           = 'BOX_'
tokenseperator       = '_'
item.regex           = '(\d\d\d[rv]?_\d?).jpg'
page.path            = 'td';
page.prefix          = 'JB';

path.image.export    = 'zimages'
path.mw.importer     = 'w/maintenance/importDump.php'
path.slicer          = 'scripts/Slicer/slicer.pl'
path.csv.import      = 'cbp-data/import/metatada-table.txt'
path.image.import    = 'cbp-data/import/images'
path.xml.export      = 'cbp-data/import/xml'
path.logs            = 'cbp-data/logs'
path.archive         = 'cbp-data/archive'

[production : common]

[staging : common]

[development : common]
host        = cbp-transcription-desk.local
admin.email =
path.prefix = '/web/site/htdocs'


database.params.username = transcription
database.params.password = transcription
database.params.dbname   = transcription

[development : common]


;*/
;?>