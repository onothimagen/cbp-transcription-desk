#!/usr/bin/perl  

use FindBin '$Bin';
use Cwd 'abs_path';
use Getopt::Std;

getopt('f');
                                                                                                               
require("$Bin/slice.pl");                                                                                           

my $file = $opt_f;
die "$file is not a JPEG file" unless ($file =~ /\.jpg$/);

my $file_full_path = abs_path( $file );
my $file_zdir = $file_full_path;
$file_zdir =~ s/\.jpg$//;

print "Processing $file_full_path\n";
print "Creating directory $file_zdir\n";

$fault = slice( $file_full_path );                                                                  
warn $fault if $fault;          

system("mv slice $file_zdir");                                                                               
