# cbp-transcription-desk
======================

Mediawiki based transcription tools


## Mediawiki Configuration instructions

### Requirements

In addition to the requirements needed to install MediaWiki, your server will also need

* To have Perl available with the ImageMagick module enabled
* To have the Apache user able to write to directories
* Ensure that passthru has not been disabled in php.ini ( see disable_functions )

### Install Mediawiki

Below, is a very summarised instructions for the the installation of MediaWiki. For full instructions goto:

[http://www.mediawiki.org/wiki/Manual:Installation_guide](http://www.mediawiki.org/wiki/Manual:Installation_guide)

[Download the latest version of Mediawiki](http://www.mediawiki.org/wiki/Download). The Transcription Desk has been tested with 1.23.1.

The extensions and skin may not work with versions earlier than 1.23.x

Copy the Mediawiki files to a subdirectory of your website root e.g. /w

Configure .htaccess and upload to your website root.

Run the MediaWiki installer wizard by going to your website root. The default settings can be used.

On completion, move the generated file LocalSettings.php to your media wiki directory e.g. /w

Make sure $wgScriptPath is configured as follows:

```
$wgScriptPath       = "/w"
```

'w' is the real directory containing your wiki files

Then ensure you add:

```
$wgArticlePath = "/td/$1";
$wgUsePathInfo = true;        # Enable use of pretty URLs
```

underneath your default configuration.

Go to the website root and the default Mediawiki website should be displayed. For example:

http://cbp-transcription-desk.local/w/index.php/Main_Page

If you are experiencing problems, try inserting the following line at the top of LocalSettings.php:

```
ini_set( 'display_errors', 'On' );
```

At this point, it is advisable that you back up your database. The following steps will make changes to the database. If you have a backup, you can roll back if any of the following steps goes wrong.

Create a 'zimages' directory in the root of your site. You should see the following in your site root:

```
w
zimages
.htaccess
```


### Install Transcription Desk files

Download the Transcription Desk files from this repository.

Copy over the contents of the '/w/extensions' and '/w/skins' directories to the respective 'skins; and 'extensions' directories in your mediawiki sub-directory

### Configure skins and extensions

Take a look at '/w/LocalSettings.example.php file to see examples of how to configure the extensions and skin in your LocalSettings.php file.

In addition to the 'skins' and 'extensions' directories there are the following directories:

cbp-data - Contains all the XML meta data and images

scripts - All the scripts needed to process the images

These should be moved to your site root.

You should see the following in your site root:

```
cbp-data
zimages
scripts
w
.htaccess
```


### How to Import Transcription Desk 'Sample Image' Instructions Page into Mediawiki

WARNING: If you have not yet backed up your database so you can roll back if there are problems with the installer.

```
cd w/maintenance
```

```
php importDump.php ../../cbp-data/Transcription_Desk_Installation_Pages.xml
```

If all is well then you should see the following:

```
Done!
You might want to run rebuildrecentchanges.php to regenerate RecentChanges
```

Refresh the main page. A 'Transcription Desk' link should appears in the left panel under 'Navigation'.

Please follow the instructions below before clicking on the link.


## How to slice and import images into Mediawiki ##

### Rename the configuration file ###

Rename:

```
./scripts/importer/config.ini.example.php
```
to
```
./scripts/importer/config.ini.php
```

### Edit the configuration file ###

The configuration file config.ini.php is an ini file with several sections.

The first section is [common]. This contains configuration items common to or shared between all environments.

After that there can be any number of sections for each environment being deployed to. There are some placeholders for common environment names and some example configuration items under [development : common].

The importer identifies the environment to use by using the 'active.section' value. The environment sections inherit all values from the [common] section which can then be overridden.

For the purposes of running the demo installer, most sections can be left as is apart from 'path.prefix', which should be an absolute path to your website's root, and the 'database.params' for your local database configuration.

Make sure that the following directories:

* path.image.export
* path.xml.export
* path.logs
* path.archive

..are writeable by the web server.

### Run the installer ###

Now click on the link 'Transcription Desk' as shown in your mediawiki ( described above ). A console should pop up in a new browser window. Click on the button 'Run new job'. The progress of the install should be displayed in the console. If all goes well then the last console message displayed will be 'Import into MW successful'.

If you look in the directory you set as 'path.image.export' in config.ini.php, you should see the images tiles in their respective directories.


### View and zoom the tiled image ###

Go your siite home page. For example:

 http://cbp-transcription-desk.local/w/index.php/Main_Page

Now click on the link to TD/001/001/001. This should display a zoom window containing the tiled images.





























