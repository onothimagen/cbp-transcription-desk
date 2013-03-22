#!/usr/bin/perl

my $usage = "metatadatapagemaker.pl -f file_with_list_of_image_file_names -l metadata_lookup_file -m [metadata-only (default n)]\n";

use Getopt::Std;
getopts('f:l:m');

die "No such input file $opt_f\n$usage\n" unless -e $opt_f;
die "No such lookup file $opt_l\n$usage\n" unless -e $opt_l;
die $usage unless $opt_f || $opt_l;

my @fieldnames;
my $lut = make_lookup_table ( $opt_l, \@fieldnames );
my @all_keys;
normalise_fieldnames (\@fieldnames); 
open FILE_LIST, $opt_f or die "Can't open $opt_f\n";
print_mwxml_header();
while (<FILE_LIST>) {
 	unless (/^(\d\d\d_\d\d\d_\d\d\d)/) {
 		next;
 	} else {
 		push @all_keys, $1;
        }
}
close FILE_LIST;
foreach my $K(@all_keys) {
	my $img;
	my $key = $K;
 	$key =~ s/_(\d\d\d)$//;
 	my $lookup_key = $key;
 	$img = $1;
 	if ($lut->{$key}) {
			# print $lut->{ $key };
		} elsif ($lut->{ $key . "a"}) {
			$lookup_key = $key . "a";
		} elsif ($lut->{ $key . "b"}) {
			$lookup_key = $key . "b";
		} elsif ($lut->{ $key . "r"}) {
			$lookup_key = $key . "r";
		} elsif ($lut->{ $key . "s"}) {
			$lookup_key = $key . "s";
		} elsif ($lut->{ $key . "v"}) {
			$lookup_key = $key . "v";
		} elsif ($lut->{ $key . "bv"}) {
			$lookup_key = $key . "bv";
		} else {
			print STDERR "$key NOT FOUND\n"; 
			next;
	} 
	my $next;
	my $prev;
        ($next, $prev) = next_prev( $K, \@all_keys);
        $next = "JB/" . $next;
        $prev = "JB/" . $prev;
        $next =~ s/_/\//g;
        $prev =~ s/_/\//g;
	print_xml_pages ($key, $img, $lut->{$lookup_key}, \@fieldnames, $opt_m, $next, $prev);
}
 	
print_mwxml_footer();

exit;

sub make_lookup_table ( $luf, $fieldnames) {
	my $luf = shift;
	my $fieldnames = shift;
	my $lut;
	# my @fieldnames;
	open LOOKUP, $luf or die "Can't open $luf\n";
	$. = 0;
	while (<LOOKUP>) {
		chomp;
		if ($. == 1) {	
			@$fieldnames = split (/\|/, $_);
		} else {
			my @fieldvalues = split(/\|/, $_);
			my $key =  $fieldvalues[0] . "_" . $fieldvalues[1];	
			unless ($key =~ /\d+/) {
				# print STDERR "No valid key at line $. ($key).\n";
				next;
			}
			$lut->{$key} = join("|", @fieldvalues);
			# print $lut->{$key}, "!!\n";
		}
	}	
	close LOOKUP;
	return $lut;
}

sub print_xml_pages ($key, $img, $record, $fieldnames, $metadata_only) {
	my $key = shift;
	my $img = shift;
	my $record = shift;
	my $fn = shift;
	my $metadata_only = shift;
	my $next = shift;
	my $prev = shift;
	
	my $ref = $key . "_" . $img;
	$ref =~ s/\_/\//g;
	$ref = "JB/" . $ref;
	$record =~ s/\&/\&amp\;/g;
	$record =~ s/\</\&lt\;/g;
	$record =~ s/\>/\&gt\;/g;
	$record =~ s/\[/\&\#91\;/g;
	$record =~ s/\]/\&\#93\;/g;
	if ($record =~ /recto/i) { $record=lc $record;}
	if ($record =~ /verso/i) { $record=lc $record;}
	my @rec = split(/\|/, $record);
	
	print_md_page_top($ref);
	for(my $i=0; $i<=$#$fn;$i++) {
		print "| ", $$fn[$i], " = " , $rec[$i], "\n";
	}
	print "| image_number = $img\n";
	print "| identifier = $ref\n";
	print "| next = $next\n";
	print "| prev = $prev\n";
	print_md_page_bottom($ref);
	unless ($metadata_only) {
		print_edit_page($ref);
		}
}
	

sub print_edit_page( $ref ) {
my $ref = shift;
my $img = $ref . ".jpg";
$img =~ s/\//\_/g;
print <<EDITPAGE;
  <page>
    <title>$ref</title>
    <revision>
      <contributor>
        <username>BenthamBot</username>
      </contributor>
      <comment>Auto loaded</comment>
      <text xml:space="preserve">
'''[{{fullurl:$ref|action=edit}} Click Here To Edit]'''
&lt;!-- ENTER TRANSCRIPTION BELOW THIS LINE --&gt;

''This Page Has Not Been Transcribed Yet''



&lt;!-- DO NOT EDIT BELOW THIS LINE --&gt;
{{Metadata:{{PAGENAME}}}}</text>
    </revision>
  </page>
EDITPAGE
}

sub print_md_page_top ($ref) {
my $ref = shift;
print <<MDPAGETOP;
 <page>
    <title>Metadata:$ref</title>
    <revision>
      <contributor>
        <username>BenthamBot</username>
      </contributor>
      <comment>Auto upload</comment>
      <text xml:space="preserve">{{Infobox Folio New
MDPAGETOP
}

sub print_md_page_bottom ($ref) {
my $ref = shift;
print <<MDPAGEBOTTOM;
}}</text>
    </revision>
  </page>
MDPAGEBOTTOM
}

sub normalise_fieldnames( $fieldnames ) {
	my $fn = shift;
	for(my $i=0; $i<=$#$fn;$i++) {
		chomp;
		$$fn[$i] =~ s/^\W+//;
		$$fn[$i] =~ s/[ \-]/_/g;
		$$fn[$i] =~ s/[\/\(\)]//g;
		$$fn[$i] = lc $$fn[$i];
		# print $$fn[$i], "\n";
	}
}

sub print_mwxml_header( ) {
	print <<MWXMLHEADER;
<mediawiki xmlns="http://www.mediawiki.org/xml/export-0.3/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.mediawiki.org/xml/export-0.3/ http://www.mediawiki.org/xml/export-0.3.xsd" version="0.3" xml:lang="en">
  <siteinfo>
    <sitename>Transcribe Bentham Transcription Desk</sitename>
    <base>http://www.benthamproject.da.ulcc.ac.uk/testwiki/index.php/Transcribe_Bentham</base>
    <generator>MediaWiki 1.15.1</generator>
    <case>first-letter</case>
    <namespaces>
      <namespace key="-2">Media</namespace>
      <namespace key="-1">Special</namespace>
      <namespace key="0" />
      <namespace key="1">Talk</namespace>
      <namespace key="2">User</namespace>
      <namespace key="3">User talk</namespace>
      <namespace key="4">Transcribe Bentham Transcription Desk</namespace>
      <namespace key="5">Transcribe Bentham Transcription Desk talk</namespace>
      <namespace key="6">File</namespace>
      <namespace key="7">File talk</namespace>
      <namespace key="8">MediaWiki</namespace>
      <namespace key="9">MediaWiki talk</namespace>
      <namespace key="10">Template</namespace>
      <namespace key="11">Template talk</namespace>
      <namespace key="12">Help</namespace>
      <namespace key="13">Help talk</namespace>
      <namespace key="14">Category</namespace>
      <namespace key="15">Category talk</namespace>
      <namespace key="100">Metadata</namespace>
      <namespace key="101">Metadata talk</namespace>
      <namespace key="102">Property</namespace>
      <namespace key="103">Property talk</namespace>
      <namespace key="104">Type</namespace>
      <namespace key="105">Type talk</namespace>
      <namespace key="108">Concept</namespace>
      <namespace key="109">Concept talk</namespace>
      <namespace key="200">UserWiki</namespace>
      <namespace key="202">User profile</namespace>
    </namespaces>
  </siteinfo>
MWXMLHEADER
}

sub print_mwxml_footer( ) {
  	print "</mediawiki>\n";
}
  
sub next_prev ( $key, $all_keys ) {
	my $key = shift;
	my $all_keys = shift;
	my @all_keys_copy = @$all_keys;
	# print "LOOKING FOR $key\n";

        my $j = 0;
	foreach my $k (@$all_keys) {
	        # print "  " . $k, "\n";
		if ( $key eq $k ) {
		       my $n = $j;
		       $n=$n+1;
		       if ($n > $#all_keys_copy ) {
		       		$n = 0;
		       }
		       my $p = $j;
		       $p = $p-1;
			# print " p = $p : n = $n : I AM " . $$all_keys[$j] . " (" . $key . ")" . "NEXT= " . $all_keys_copy[$n] . ". PREV = " . $all_keys_copy[$p] . ".\n";	
			return ($all_keys_copy[$n], $all_keys_copy[$p]);
		} else {	
			$j++;
			}
	}	
}	