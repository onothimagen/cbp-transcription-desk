
// [http://{{SERVERNAME}}/scripts/installer/index.php Run the demo image uploader]

var Element;

Element = $('a').filter(function(index) { return $(this).text() === "Run the demo image uploader"; });

$( Element ).click(function() {
	window.open( '/scripts/installer/index.php','installer','width=700,height=235');
    return false;
});
