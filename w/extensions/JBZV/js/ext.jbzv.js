/**
 * Copyright (C) 2013 Richard Davis
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License Version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Richard Davis <r.davis@ulcc.ac.uk>
 * @author Ben Parish <b.parish@ulcc.ac.uk>
 * @copyright 2013 Richard Davis
 */

/* Re-organises the elements to the correct layout after the wikiEditor has loaded */

$(document).ready(function(){
	
	var $style_sheet = 'extensions/JBZV/js/ext.jbzv.css';
		
	if (document.createStyleSheet){
		document.createStyleSheet( $style_sheet );
	}
	else {
		$("head").append($("<link rel='stylesheet' href='" + $style_sheet + "' type='text/css' media='screen' />"));
	}

});



































