// Doesn't work with Opera
//http://www.codekites.com/ajax-based-streaming-without-polling/

function ajaxConnect(){

    var ie         = false;
    var xhr;  // The variable that makes Ajax possible!
    var ajaxURL    = '/scripts/importer/index.php' + '?' + Math.random();
    var returnText = '';;

    try{
        xhr = new XDomainRequest();
        ie = true;
    } catch (e) {
      try{
          xhr = new XMLHttpRequest();
      } catch (e){
        // Something went wrong
        alert("Your browser broke!");
        return false;
      }
    }

    if (ie) {
    	clearConsole();
        xhr.timeout = 1000000;
        xhr.open( "GET", ajaxURL, true);
        document.getElementById( 'loader' ).style.display = 'block';
        
        xhr.onprogress = function() {
            ajaxReturnText( xhr.responseText );
        };
        
        xhr.onload = function() {
            document.getElementById('loader').style.display = 'none';
        };
        
        xhr.send(null);

        window.clearTimeout( scrollConsole );
        
        // Make sure it reaches the end
        ScrollConsole();

    }else{

        // Create a function that will receive data sent from the server
        xhr.onreadystatechange = function(){

            console.log(xhr.readyState);

            if( xhr.readyState = 2 ){
            	clearConsole();
                document.getElementById( 'loader' ).style.display = 'block';
            }

            if( xhr.readyState > 2 && xhr.status == 200 ){

            	returnText = xhr.responseText;
            	
            	returnText = returnText.replace(/\r\n?|\n/g, "<br />" );

               	ajaxReturnText( returnText );
            }

            // Stop scrolling console when all output has been displayed.
            if( xhr.readyState == 4  ){
                window.clearTimeout( scrollConsole );
                // Make sure it reaches the end
                ScrollConsole();
                document.getElementById( 'loader' ).style.display = 'none';
            }
        }
        xhr.open( "GET", ajaxURL, true );
        xhr.send('');
    }
}

function clearConsole(){
	document.getElementById('console').innerHTML = '';
}

function ajaxReturnText( ajaxOutput){
   document.getElementById('console').innerHTML = ajaxOutput;
}


function ScrollConsole(){

	var scrollHeight =  $('#console').prop( "scrollHeight" );

	var scrollTop    = $('#console').prop( "scrollTop" );

	var distance     = scrollTop + ( scrollHeight - scrollTop );

	$('#console').animate({ scrollTop: distance }, 500);

}

var scrollConsole;

function StartScroll(){

	ScrollConsole();

	// Don't make this to slow otherwise animation is lost

	scrollConsole = setTimeout( "StartScroll()", 1000 );

}












