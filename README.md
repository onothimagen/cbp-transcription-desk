# cbp-transcription-desk
======================

Mediawiki based transcription tools


## Installation instructions

### Installing Mediawiki

NOTE. Below, is a very summarised isntructions for the the installation of MediaWiki
      For full instructions goto:

[http://www.mediawiki.org/wiki/Manual:Installation_guide](http://www.mediawiki.org/wiki/Manual:Installation_guide)

Download the latest version of Mediawiki. The Transcription Desk has been tested with 1.20.5.

The extensions and skin may not work with versions earlier than 1.20.x

Install the Mediawiki files to a subdirectory of your website root e.g. /w

Configure .htaccess and upload to your website root.

Run installer wizard by going to your website root. The default settings can be used.

On completion, move the generated file LocalSettings.php to your media wiki directory under the root.

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

Go to the website root and the default Mediawiki website should be displayed.

If you are experiencing problems, try inserting the following line at the top of LocalSettings.php:

ini_set( 'display_errors', 'On' );

Create a 'zimages' directory in the root of your site.


### Upload Transcription Desk files

Move the contents of the extensions and skins directories under your mediawiki sub-directory

e.g. /w/extensions and /w/skins

### Configure skins and extensions

Take a look at LocalSettings.example.php file to see examples of how to configure the extensions and skin




### Import Transscription Desk 'Sample Image' Instructions Page

```
cd w/maintenance

php importDump.php ../../cbp-data/Transcription_Desk_Installation_Pages.xml

Refresh the main page. A 'Transcription Desk' link should appears in the left panel under 'Navigation'
```