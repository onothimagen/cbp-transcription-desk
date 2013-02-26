#!/usr/bin/perl

###############################################################################
#
# slice.pl 
#
# ----------------------------------------------------------------------------
# Copyright (c) W.S. Packaging, Inc.          -- www.wspackaging.com
# Business & Marketing Development Department -- webflex(AT)wspackaging.com
# This code is free to use and extend, providing this seven-line copyright 
# message remain intact. No warranties are given, expressed or implied. 
# Go Packers.
# ----------------------------------------------------------------------------
#
# Initial zoomer pre-processing function. It requires a filename; it will
# assume that zoom content will need to be made in that same dir. This will
# just create a directory called $slice filled with zoom content and the xml
# to match, so consider changing $slice as you see fit.
#
# call using: require("slice.pl");
#	      $fault = slice("/full/path/to/source/image");
#	      warn $fault if $fault;
# supports standard perl eval error handling. 
#
#
###############################################################################

use strict;

sub slice {

	use File::Path;
	use File::Basename;
	use Cwd;
	use Image::Magick;

	my $importantCWD = cwd(); # as a way to get around potential file system issues, we're going to change
				  # our relative path

	my $retVal = '';
	my $slice = 'slice'; # you may wish to change this

	my $sysMove = 'mv';
	
	if( $^O eq 'MSWin32' ){
		$sysMove = 'move';
	}

###############################################################################
#
# First, lets establish that we've got everything we need to proceed, but not
# too much.
#
###############################################################################

	if ($#_ > 1) { $retVal = "slice: Too many arguements (I only take one, two tops, thanks)"; return $retVal; }
	my $sourceImg = $_[0];

	unless (-e $sourceImg) { $retVal = "slice: Source image '$sourceImg' unfound"; return $retVal; }

	my @suffixen = (".png",".jpg",".tif");
	my ($srcName, $srcPath, $srcSuffix) = fileparse($sourceImg, @suffixen);

	# look for the $slice dir. if it's there, blow it away. there was likely a very
	# good reason that we got called, so we should make new content.
	
	if (-d "$srcPath/$slice") { 
		eval { rmtree("$srcPath/$slice") };
		if ($@) {
			$retVal = "slice: Couldn't remove existing $srcPath/$slice: $@";
			return $retVal;
		}
	}

	# make the $slice dir
	# give the option to pass two args

	my $outputPath;

	if ($_[1]) {
		$outputPath = $_[1];
		unless (-d $outputPath) { $retVal = "slice: $outputPath does not look like a directory"; return $retVal; }
		$outputPath = $outputPath.$slice;
	} else {
		chdir $srcPath or die "slice: chdir $srcPath died:$!\n";
		$outputPath = "./$slice";
	}

	eval { mkpath($outputPath) };
	if ($@) {
		$retVal = "slice: Couldn't create $outputPath: $@";
		return $retVal;
	}

###############################################################################
#
# Meat n potatos time.
#
###############################################################################
	
	my $source = Image::Magick->new;
	my $tileSize = 256; # don't change this unless Dave@Zoomify says its okay
	
	my $fault = $source->Read($sourceImg);
	if ($fault) {return $fault;}
	
	my ($srcWidth, $srcHeight) = $source->Get('columns','rows');
	my ($xmlWidth, $xmlHeight) = ($srcWidth, $srcHeight);
	my ($x, $y) = ($srcWidth, $srcHeight);

	my $levels = 0;
	my $scale = .5;
	# find out how many levels of chopping we'll need to do. in other words,
	# how many downscales does it take to get to something smaller than
	# $tileSize?
	while ($x > $tileSize || $y > $tileSize) {
		$x = $srcWidth; $y = $srcHeight;
		$x = $x * $scale;
		$y = $y * $scale;
		if ($x > $tileSize || $y > $tileSize) { 
			$scale = $scale / 2;
		}
		$levels++;
	}
	
	
	my $ratio = 1;
	
	# main block to do the chopping. we start at the BIGGEST size (aka $levels)
	# and work our way down until we have an image that shouldn't need to be 
	# chopped at all. this SHOULD be $levels+1 iterations.
	
	# now for the chopper itself. the logic is as follows:
		
	# set row / column markers for file naming (starting with zero)
	my $colNum = 0; my $rowNum = 0; my $numTiles = 0;
	my ($i, $chopWidth, $chopHeight, $rowSTOP, $rowNum, $tile, $tmpFileName);

	for ($i = $levels; $i >= 0; $i--) {
		# unless this is the first time through, we need to scale the $source image down
		# don't forget to set $ratio at the bottom of the 'for' loop.
		unless ($ratio == 1) {
			$srcWidth = int($srcWidth / 2);
			$srcHeight = int($srcHeight / 2);
			$fault = $source->Resize(width=>$srcWidth, height=>$srcHeight);
			if ($fault) { return $fault;}
		}
	
		# set x/y markers.
		my $xMark = 0; my $yMark = 0; my $colSTOP = 0; my $colNum = 0;
	
		# move column by column, until the $colSTOP var is triggered
		while ($colSTOP == 0) {
			# if image width - the x marker is less than the tile size, use that as the width to chop
			# AND SET THE STOP VAR...THIS IS THE LAST TIME THROUGH!!!
			#
			# ...otherwise, use the defaults
		
			if ($srcWidth - $xMark <= $tileSize) { 
				$chopWidth = $srcWidth - $xMark;
				$colSTOP = 1;
			} else { $chopWidth = $tileSize; }
		
			# Now within the rows in the column, same STOP idea. Also set the row to zero. Always zero
			# when beginning a row iteration
			$rowSTOP = 0;
			$yMark = 0;
			$rowNum = 0;
			while ($rowSTOP == 0) {
			
				# if image height - the y marker is less than the tile size, use that as the height to chop
				if ($srcHeight - $yMark <= $tileSize) {
					$chopHeight = $srcHeight - $yMark;
					$rowSTOP = 1;
				} else { $chopHeight = $tileSize; }
			
				# chop a single file out based on the x/y markers and the size to chop from the above two tests
				$tile = $source->Clone();
				$fault = $tile->Crop(width=>$chopWidth, height=>$chopHeight,x=>$xMark,y=>$yMark);
				if ($fault){ return $fault;}

				# name a file based on the row/column markers (X-Y-Z.jpg)
				$tmpFileName = $i."-".sprintf("%02d",$colNum)."-".sprintf("%02d",$rowNum).".jpg";
				$fault = $tile->Write(filename=>"$outputPath/$tmpFileName", quality=>'90');
				undef $tile;
				if ($fault) { return $fault;}

				$numTiles++;
				
				# move the y marker. if it results in a negative value because we're at the end,
				# it's no big deal. it'll get reset when we break out of the row while( ) and 
				# start a new column
	
				$yMark = $yMark + $tileSize;
				$rowNum++;
			}
			
			# move the x marker over to the next column
			$xMark = $xMark + $tileSize;
			$colNum++;
		}
		$ratio = $ratio * 2;
	}
	undef $source; #AND THE KERNEL BREATHES A SIGH OF RELIEF! HURRAH!
	
###############################################################################
#
# yaaay! now for the hard part. we need to separate everything into subdirs...
# don't ask how this is actually accomplished. 
# 
###############################################################################

	my $dir = $outputPath;
	my $dirTotal = 0; my $tileGroupNum = 0;
	my $mkdirName = "$dir/TileGroup";
	my $tileGroup = $mkdirName.$tileGroupNum;

	eval { mkdir ($tileGroup) };
	if ($@) {
		$retVal = "slice: Cannot create $tileGroup: $@";
		return $retVal;
	}
	
	my $backslash    = '\\';
	my $forwardslash = '\/';

	my ($arg1, $arg2, $count, $fattestNode, $file, @nodes, $j, @stuff, $tmpfile, @newfile, @moveCMD);
	for ($i = 0; $i <= $levels; $i++) {
		$count = 0;
		$fattestNode = 0;
		while (<$dir/$i*.jpg>) {
			$count++;
			$file = $_;
			$file =~ s/$dir\///;
			$file =~ s/\.jpg//;
			@nodes = split(/-/,$file);
			if (int($nodes[2]) > $fattestNode) { $fattestNode = int($nodes[2]); }
			
		}
	
		for ($j = 0; $j <= $fattestNode; $j++) {
		
			if ($j < 10) { $j = sprintf("%02d",$j); }
			@stuff = <$dir/$i-*-$j.jpg>;
			
			foreach $file (sort @stuff) {
				$file =~ s/$dir\///;
				$tmpfile = $file;
				$tmpfile =~ s/\.jpg//;
				@newfile = split(/-/,$tmpfile);
				$newfile[0] = int($newfile[0]);
				$newfile[1] = int($newfile[1]);
				$newfile[2] = int($newfile[2]);
				$dirTotal++;
		
				$arg1 = "$outputPath/$file";
				$arg2 = "$tileGroup/$newfile[0]-$newfile[1]-$newfile[2].jpg";
				
				# Reverses slashes in Windows
				if( $^O eq 'MSWin32' ){
					$arg1 =~ s/$forwardslash/$backslash/g;
					$arg2 =~ s/$forwardslash/$backslash/g;
				}
				
				@moveCMD = ("$sysMove", $arg1, $arg2);
				
				system(@moveCMD) == 0 or return "slice: @moveCMD failed: $?";
				
				if ($dirTotal == 256) {
					$tileGroupNum++;
					$tileGroup = "$mkdirName$tileGroupNum";
					
					eval { mkdir ($tileGroup) };
					if ($@) {
						$retVal = "slice: Cannot create $tileGroup: $@";
						return $retVal;
					}
					$dirTotal = 0;
				}
			}
		}
	}

	#assuming everyting else went well, create the XML and
	#call it a day

	open (XML, ">$outputPath/ImageProperties.xml") or return "slice: couldn't creat $outputPath/ImageProperties.xml: $!";
	print XML "<IMAGE_PROPERTIES WIDTH=\"$xmlWidth\" HEIGHT=\"$xmlHeight\" NUMTILES=\"$numTiles\" VERSION=\"1.8\" TILESIZE=\"$tileSize\" />";
	close XML;

	chdir $importantCWD or die "Couldn't chdir back where we started in $importantCWD: $!";

	return '';
}
1;
