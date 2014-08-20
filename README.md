# cbp-transcription-desk
======================

Mediawiki based transcription tools


## Mediawiki Configuration instructions

NOTE: The extensions and skin may not work with Mediawiki versions earlier than 1.23.x

### Requirements

In addition to the requirements needed to install MediaWiki, your server will also need:

* to have Perl available with the ImageMagick module enabled
* to have the Apache user able to write to directories
* ensure that passthru has not been disabled in php.ini ( see disable_functions )
* ensure that both Apache and PHP are configured to allow long running scripts

### Download Mediawiki

[Download the latest version of Mediawiki](http://www.mediawiki.org/wiki/Download). The Transcription Desk has been tested with 1.23.2.

Copy the Mediawiki files to a subdirectory of your website root e.g. /w

### Download Transcription Desk files

Download the Transcription Desk files from this repository.

Copy .htaccess to your website's root. It has been configured to use 'w' as the mediawiki directory. If you have used a different directory name then configure it to use this instead.

### Install Mediawiki

NOTE: These are very basic instructions for the the installation of MediaWiki. For full instructions see [http://www.mediawiki.org/wiki/Manual:Installation_guide](http://www.mediawiki.org/wiki/Manual:Installation_guide)

Run the MediaWiki installer wizard by going to your website root. The default settings can be used.

On completion of the install, you will be prompted to download the generated file LocalSettings.php. Save it to your Mediawiki directory e.g. /w

Make sure $wgScriptPath is configured with your Mediawiki install directory name. For example, using the default '/w' folder:

```
$wgScriptPath       = "/w"
```

Then ensure you add:

```
$wgArticlePath = "/td/$1";
$wgUsePathInfo = true;        # Enable use of pretty URLs
```

underneath your default configuration.

Go to the website root and the default Mediawiki website should be displayed. For example:

http://cbp-transcription-desk.local/w/index.php/Main_Page

At this point, it is advisable that you back up your database. The following steps will make changes to the database. If you have a backup, you can roll back if any of the following steps goes wrong.

If you are experiencing problems, try inserting the following line at the top of LocalSettings.php:

```
ini_set( 'display_errors', 'On' );
```

Create a 'zimages' directory in the root of your site. You should then see the following in your site root:

```
w
zimages
.htaccess
```

### Install Transcription Desk files

From the Transcription Desk files downloaded earlier from the repository, copy over the contents of the '/w/extensions' and '/w/skins' directories to the respective 'skins; and 'extensions' directories in your mediawiki sub-directory

In addition to the 'skins' and 'extensions' directories there are the following directories:

cbp-data - Contains all the XML meta data and images

scripts - All the scripts needed to process the images

These should also be moved to your site root.

You should see the following in your site root:

```
cbp-data
zimages
scripts
w
.htaccess
```

### Configure skins and extensions

Take a look at '/w/LocalSettings.example.php file to see examples of how the extensions and skin are configured. Append them to the end of the LocalSettings.php file generated and saved earlier in a previous step.


### How to Import Transcription Desk 'Sample Image' Instructions Page into Mediawiki

WARNING: If you have not yet backed up your database so you can roll back if there are problems with the installer.

Enter the following commands from the command line:

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

Log in as the Mediawiki adminstrator.

Refresh the main page. A 'Transcription Desk' link should appears in the left panel under 'Navigation'.

Please follow the instructions below before clicking on the link.

At this point, a further database backup is recommended.


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

### Load the 'importer' schema ###

Load
```
./importer/schemas/importer_schema.sql
```
..into your MySQL database.


### Edit the configuration file ###

The configuration file config.ini.php is an ini file with several sections.

The first section is [common]. This contains configuration items common to or shared between all environments.

After that there can be any number of sections for each environment being deployed to. There are some placeholders for common environment names and some example configuration items under [development : common].

The importer identifies the environment to use by using the 'active.section' value. The environment sections inherit all values from the [common] section which can then be overridden.

For the purposes of running the demo installer, most sections can be left as is apart from:

* 'perl.path
* 'path.prefix', which should be an absolute path to your website's root
* 'database.params' for your local database configuration.

Make sure that the following directories:

* path.image.export
* path.xml.export
* path.logs
* path.archive

..are writeable by the web server.

### Run the installer ###

Now click on the link 'Transcription Desk' as shown in your mediawiki ( described above ).

You will see a list of instructions. Ensure you have completed all steps including importing the installer schema.

Click on the link ' Run the demo image uploader'

A console should pop up in a new browser window. Click on the button 'Run new job'. The progress of the install should be displayed in the console. If all goes well then the last console message displayed will be 'Import into MW successful'.

If you look in the directory you set as 'path.image.export' in config.ini.php, you should see the images tiles in their respective directories.

Once the installer has completed, the window can be refreshed and the processing jobs will be listed.


### View and zoom the tiled image ###

Go your site home page. For example:

http://cbp-transcription-desk-test.local/w/index.php/Transcription_Desk

 http://cbp-transcription-desk.local/w/index.php/Main_Page

Now click on the link to TD/001/001/001. This should display a zoom window containing the tiled images.

### Create a new account to view the JB Toolbar buttons on the editor  ###

Only 'email confirmed' users can edit pages so register as a user and confirm by clicking the link sent by email and the editor should appear next to the image.




























