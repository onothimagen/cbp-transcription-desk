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
site_name				 = 'Transcribe Bentham Transcription Desk'
home_page_name			 = Transcribe_Bentham
page_path				 = 'td';
version					 = 1.20.2
export_version			 = 0.7
admin.email				 = b.parish@ulcc.ac.uk
path.slicer              = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\Slicer\slicer.pl';
path.csv.import          = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\input\text\Bentham-Image-Metatada-Table.txt'
path.image.import		 = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\input\images'
path.image.export		 = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\export\images'
path.xml.export          = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\export\text\export.xml'
path.logs                = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\logs'
path.archives            = 'M:\Dropbox\web\sites\cbp-transcription-desk.local\scripts\tests\Files\archives'
page_prefix              = 'JB';
database.params.username = transcription
database.params.password = transcription
database.params.dbname   = transcription

;*/
;?>