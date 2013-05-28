// Doesn't work with Opera
//http://www.codekites.com/ajax-based-streaming-without-polling/

$xhrPool = [];

function abortAll(){
	
	if( $xhrPool.length > 0 ){
		document.getElementById('loader').style.display = 'none';
        window.clearTimeout( scrollConsole );
        // Make sure it reaches the end
        ScrollConsole();
	}
	
	for( x in $xhrPool ){
		$xhrPool[x].abort();
		$xhrPool.splice(x, 1);
	}
}

function ajaxConnect( action, id ){
	
	StartScroll();

    var ie           = false;
    
    var ajaxURL      = '';
    
    var job_id_param = '';
    
    if( id != null ){
    	job_id_param = '?job_id=' + id;
    }
    
    switch ( action ) {
 	   case "start" :
		   var ajaxURL    = '/scripts/importer/index.php' + '?' + Math.random();
	      break;
	   case "restart" :
		   var ajaxURL    = '/scripts/importer/index.php' + job_id_param + '&' + Math.random();
	      break;
	   case "stop" :	   
	        var ajaxURL    = '/scripts/importer/index.php' + job_id_param + '&action=stop';
	        abortAll();
	      break;
	   default :
	     alert( 'action not specified in ajaxConnect()' );
    }
   
    var returnText = '';
    

    try{
        var xhr = new XDomainRequest();
        ie = true;
    } catch (e) {
      try{
         var xhr = new XMLHttpRequest();
      } catch (e){
        // Something went wrong
        alert("Your browser broke!");
        return false;
      }
    }
    
    $xhrPool.push( xhr );

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
            
            var index = $xhrPool.indexOf( xhr );
            if (index > -1) {
                $xhrPool.splice(index, 1);
            }
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
            	document.getElementById( 'loader' ).style.display = 'none';
                var index = $.xhrPool.indexOf( xhr );
                if ( index > -1 ) {
                    $.xhrPool.splice( index, 1 );
                }
                window.clearTimeout( scrollConsole );
                // Make sure it reaches the end
                ScrollConsole();
            }
        }
        xhr.open( "GET", ajaxURL, true );
        xhr.send( "" );
        window.clearTimeout( scrollConsole );
        ScrollConsole();
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

	// Don't make this too slow otherwise animation is lost

	scrollConsole = setTimeout( "StartScroll()", 1000 );

}


$(document).ready(function() {

    $('.error').qtip({
    	content: {
    		attr: 'alt'
    	},

    	position: {
    		my: 'right center',
    		at: 'left center',
    		target: $('.error'),
    		adjust: {
    			y: -80,
    			x: 300
    		}
    	},

    	style: {
    		classes: 'qtip-light qtip-rounded',
    	}

	})
});












