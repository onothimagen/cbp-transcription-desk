;<?php
;die(); // For further security
;/*

[common]
database.adapter         = Pdo_Mysql
database.params.host     = localhost

[production : common]
webhost                  =
path.image.import        =
path.image.slice         =
database.params.username =
database.params.password =
database.params.dbname   =

[development : common]
webhost                  = cbp-transcription-desk.local
admin.email				 = b.parish@ulcc.ac.uk
path.slicer              = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\Slicer\slicer.pl';
path.csv.import          = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\input\text\Bentham-Image-Metatada-Table.txt'
path.image.import		 = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\input\images'
path.image.export		 = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\export\images'
path.xml.export          = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\export\text'
path.logs                = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\logs'
path.archives            = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\archives'
database.params.username = transcription
database.params.password = transcription
database.params.dbname   = transcription

;*/
;?>