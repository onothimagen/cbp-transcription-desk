
// [http://{{SERVERNAME}}/scripts/installer/index.html Install Example Item]

var Element;

Element = $('a').filter(function(index) { return $(this).text() === "Install Example Item"; });

$( Element ).click(function() {
	window.open( '/scripts/installer/index.html','installer','width=700,height=270');
    return false;
});
