# cbp-transcription-desk
======================

Mediawiki based transcription tools


## Installation instructions

### Requirements

In addition to the requirements needed to install MediaWiki, your server will also need to have Perl available

### Install Mediawiki

Below, is a very summarised instructions for the the installation of MediaWiki. For full instructions goto:

[http://www.mediawiki.org/wiki/Manual:Installation_guide](http://www.mediawiki.org/wiki/Manual:Installation_guide)

[Download the latest version of Mediawiki](http://www.mediawiki.org/wiki/Download). The Transcription Desk has been tested with 1.20.5.

The extensions and skin may not work with versions earlier than 1.20.x

Copy the Mediawiki files to a subdirectory of your website root e.g. /w

Configure .htaccess and upload to your website root.

Run installer wizard by going to your website root. The default settings can be used.

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


### Import Transcription Desk 'Sample Image' Instructions Page

```
cd w/maintenance

php importDump.php ../../cbp-data/Transcription_Desk_Installation_Pages.xml
```

If all is well then you should see the following:

```
Done!
You might want to run rebuildrecentchanges.php to regenerate RecentChanges
```

Refresh the main page. A 'Transcription Desk' link should appears in the left panel under 'Navigation'.

Before you click on it, rename ./scripts/importer/config.ini.example.php to ./scripts/importer/config.ini.php
