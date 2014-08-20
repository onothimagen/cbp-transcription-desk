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

/*
 * This adds the JBTEIToolbar to the deprecated Mediawiki toolbar. For more information see
 * (http://www.mediawiki.org/wiki/Customizing_edit_toolbar#How_do_I_add_more_buttons_on_the_edit_page.3F)
 */

var maxMin = '<button id="minimise" type="button" style="position:relative; float:right;">Minimise</button>'
			+ '<button id="maximise" type="button" style="position:relative; float:right;">Maximise</button>';


var $images_path = "extensions/JBTEIToolbar/images/";

var addExtraButtonsToClassicToolBar = function(){
    /*
     * The toolbar needs to be moved above the edit form so that
     * the viewer will float alongside the text area
     */

    $('#toolbar').insertBefore('#editform');

    $('#wpTextbox1').insertBefore('#zoomviewerframe');


	$('#toolbar').empty();

	mw.toolbar.addButton({
		'id'        : 'mw-editbutton-linebreak',
		'imageFile' : $images_path + 'jb-button-linebreak.png',
		'speedTip'  : mw.msg( 'toolbar-label-line-break' ),
		'tagOpen'   : '<lb/>',
		'tagClose'  : '',
		'sampleText': ''
	});

	mw.toolbar.addButton({
		'id'        : 'mw-editbutton-pagebreak',
		'imageFile' : $images_path + 'jb-button-pagebreak.png',
		'speedTip'  : mw.msg( 'toolbar-label-page-break' ),
		'tagOpen'   : '<pb/>',
		'tagClose'  : '',
		'sampleText': ''
	});

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-heading',
    	'imageFile' : $images_path + 'jb-button-heading.png',
        'speedTip'  : mw.msg( 'toolbar-label-heading' ),
        'tagOpen'   : '<head>',
        'tagClose'  : '</head>',
        'sampleText': mw.msg( 'toolbar-peri-heading' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-paragraph',
    	'imageFile' : $images_path + 'jb-button-paragraph.png',
        'speedTip'  : mw.msg( 'toolbar-label-paragraph' ),
        'tagOpen'   : '<p>',
        'tagClose'  : '</p>',
        'sampleText': mw.msg( 'toolbar-peri-paragraph' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-add',
    	'imageFile' : $images_path + 'jb-button-add.png',
        'speedTip'  : mw.msg( 'toolbar-label-addition' ),
        'tagOpen'   : '<add>',
        'tagClose'  : '</add>',
        'sampleText': mw.msg( 'toolbar-peri-addition' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-deletion',
    	'imageFile' : $images_path + 'jb-button-deletion.png',
        'speedTip'  : mw.msg( 'toolbar-label-deletion' ),
        'tagOpen'   : '<del>',
        'tagClose'  : '</del>',
        'sampleText': mw.msg( 'toolbar-peri-deletion' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-questionable',
    	'imageFile' : $images_path + 'jb-button-questionable.png',
        'speedTip'  : mw.msg( 'toolbar-label-questionable' ),
        'tagOpen'   : '<unclear>',
        'tagClose'  : '</unclear>',
        'sampleText': mw.msg( 'toolbar-peri-questionable' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-illegible',
    	'imageFile' : $images_path + 'jb-button-illegible.png',
        'speedTip'  : mw.msg( 'toolbar-label-illegible' ),
        'tagOpen'   : '<gap/>',
        'tagClose'  : '',
        'sampleText': ''
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-note',
    	'imageFile' : $images_path + 'jb-button-note.png',
        'speedTip'  : mw.msg( 'toolbar-label-note' ),
        'tagOpen'   : '<note>',
        'tagClose'  : '</note>',
        'sampleText': mw.msg( 'toolbar-peri-note' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-underline',
    	'imageFile' : $images_path + 'jb-button-underline.png',
        'speedTip'  : mw.msg( 'toolbar-label-underline' ),
        'tagOpen'   : '<hi rend="underline">',
        'tagClose'  : '</hi>',
        'sampleText': mw.msg( 'toolbar-peri-underline' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-superscript',
    	'imageFile' : $images_path + 'jb-button-superscript.png',
        'speedTip'  : mw.msg( 'toolbar-label-superscript' ),
        'tagOpen'   : '<hi rend="superscript">',
        'tagClose'  : '</hi>',
        'sampleText': mw.msg( 'toolbar-peri-superscript' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-sic',
    	'imageFile' : $images_path + 'jb-button-sic.png',
        'speedTip'  : mw.msg( 'toolbar-label-spelling' ),
        'tagOpen'   : '<sic>',
        'tagClose'  : '</sic>',
        'sampleText':  mw.msg( 'toolbar-peri-spelling' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-foreign',
    	'imageFile' : $images_path + 'jb-button-foreign.png',
        'speedTip'  : mw.msg( 'toolbar-label-foreign' ),
        'tagOpen'   : '<foreign>',
        'tagClose'  : '</foreign>',
        'sampleText': mw.msg( 'toolbar-peri-foreign' )
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-ampersand',
    	'imageFile' : $images_path + 'jb-button-ampersand.png',
        'speedTip'  : mw.msg( 'toolbar-label-ampersand' ),
        'tagOpen'   : '&amp;',
        'tagClose'  : '',
        'sampleText': ''
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-longdash',
    	'imageFile' : $images_path + 'jb-button-longdash.png',
        'speedTip'  : mw.msg( 'toolbar-label-long-dash' ),
        'tagOpen'   : '&#x2014;',
        'tagClose'  : '',
        'sampleText': ''
    });

    mw.toolbar.addButton({
    	'id'        : 'mw-editbutton-comment',
    	'imageFile' : $images_path + 'jb-button-comment.png',
        'speedTip'  : mw.msg( 'toolbar-label-comment' ),
        'tagOpen'   : '<!-- ',
        'tagClose'  : ' -->',
        'sampleText': 'user comment'
    });

}



/*
 * BP 2013
 * This is for the new WikiEditor Extension if has been enabled
 * The following lines remove the existing sections and buttons.
 * The WikiEditor is then populated with the JB TEI button.
 * NOTE: For this to work the WikiEditor must be included
 * before this JBTEIToolBar extension is included. For example:
 *
 * require_once( 'WikiEditor/WikiEditor.php' );
 * $wgDefaultUserOptions['usebetatoolbar'] = 1;
 *
 * require_once( 'JBTEIToolbar/JBTEIToolbar.php' );
 *
 *
 */

var addExtraButtons = function() {

    /*
     * The toolbar needs to be moved above the edit form so that
     * the viewer will float alongside the text area
     */

    $('.wikiEditor-ui-top').insertBefore('#editform')

    $('.wikiEditor-ui').insertAfter('.wikiEditor-ui-top');

    $('#wpTextbox1').insertBefore('#zoomviewerframe');

    /*
     * Reorganise editor to fit alongside zoom viewer
     */

    var $style_sheet = 'extensions/JBTEIToolbar/css/ext.jbteitoolbar.css';

    $("body").before("<link rel='stylesheet' href='" + $style_sheet + "' type='text/css' media='screen' />");

    /*
     * This overrides the width:38% in ext.jbteitoolbar.css intended for the old editor which creates a blue gap in WikiEditor
     */

    $('#wpTextbox1').css( "width", "100%" );


	$( '#wpTextbox1' ).wikiEditor( 'removeFromToolbar', {
        'section': 'advanced'
	});

	$( '#wpTextbox1' ).wikiEditor( 'removeFromToolbar', {
        'section': 'help'
	});

	$( '#wpTextbox1' ).wikiEditor( 'removeFromToolbar', {
        'section': 'characters'
	});

	$( '#wpTextbox1' ).wikiEditor( 'removeFromToolbar', {
		'section': 'main',
        'group': 'insert'
	});


	$( '#wpTextbox1' ).wikiEditor( 'removeFromToolbar', {
		'section': 'main',
        'group': 'format',
        'tool':'italic'
	} );

	$( '#wpTextbox1' ).wikiEditor( 'removeFromToolbar', {
		'section': 'main',
        'group': 'format',
        'tool':'bold'
	} );

	var $fullPath = window.location.pathname;

	var $lastSlashIndex = $fullPath.lastIndexOf('/');

	var $mw_root_directory = $fullPath.substring(0, $lastSlashIndex + 1 );

	var $path = $mw_root_directory + $images_path;

	$( '#wpTextbox1' ).wikiEditor( 'addToToolbar', {
		'section': 'main',
        'group'  : 'format',
        'tools'  : {

	        	'line-break': {
	        		label :  mw.msg( 'toolbar-label-line-break' ),
	        		type  : 'button',
	        		icon  : $path + 'jb-button-linebreak.png',
	        		action: {
	        			type   : 'encapsulate',
	        			options: {
	        				pre:  '<lb/>',
	        				peri: '',
	        				post: '',
	        			}
	        		}
	        	},

	        	'pagebreak': {
	        		label : mw.msg( 'toolbar-label-page-break' ),
	        		type  : 'button',
	        		icon  : $path + 'jb-button-pagebreak.png',
	        		action: {
	        			type   : 'encapsulate',
	        			options: {
	        				pre:  '<pb/>',
	        				peri: '',
	        				post: '',
	        			}
	        		}
	        	},

	            'heading': {
	                label : mw.msg( 'toolbar-label-heading' ),
	                type  : 'button',
	                icon  : $path + 'jb-button-heading.png',
	                action: {
	                        type   : 'encapsulate',
	                        options: {
	                            pre:  '<head>',
	                            peri: mw.msg( 'toolbar-peri-heading' ),
	                            post: '</head>',
	                        }
	                }
	            },

	            'paragraph': {
	                label : mw.msg( 'toolbar-label-paragraph' ),
	                type  : 'button',
	                icon  : $path + 'jb-button-paragraph.png',
	                action: {
	                        type   : 'encapsulate',
	                        options: {
	                            pre:  '<p>',
	                            peri: mw.msg( 'toolbar-peri-paragraph' ),
	                            post: '</p>',
	                        }
	                }
	            },

			    'addition': {
			        label : mw.msg( 'toolbar-label-addition' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-add.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre:  '<add>',
			                    peri: mw.msg( 'toolbar-peri-addition' ),
			                    post: '</add>',
			                }
			        }
			    },

			    'deletion': {
			        label : mw.msg( 'toolbar-label-deletion' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-deletion.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre:  '<del>',
			                    peri: mw.msg( 'toolbar-peri-deletion' ),
			                    post: '</del>',
			                }
			        }
			    },

			    'questionable': {
			        label : mw.msg( 'toolbar-label-questionable' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-questionable.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre:  '<unclear>',
			                    peri: mw.msg( 'toolbar-peri-questionable' ),
			                    post: '</unclear>',
			                }
			        }
			    },

			    'illegible': {
			        label : mw.msg( 'toolbar-label-illegible' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-illegible.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre:  '<gap/>',
			                    peri: '',
			                    post: '',
			                }
			        }
			    },

			    'note': {
			        label : mw.msg( 'toolbar-label-note' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-note.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre:  '<note>',
			                    peri: mw.msg( 'toolbar-peri-note' ),
			                    post: '</note>',
			                }
			        }
			    },

			    'underline': {
			        label : mw.msg( 'toolbar-label-underline' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-underline.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre:  '<hi rend="underline">',
			                    peri: mw.msg( 'toolbar-peri-underline' ),
			                    post: '</hi>'
			                }
			        }
			    },

			    'superscript': {
			        label : mw.msg( 'toolbar-label-superscript' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-superscript.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                	pre : '<hi rend="superscript">',
			                    peri: mw.msg( 'toolbar-peri-superscript' ),
			                    post: '</hi>'
			                }
			        }
			    },

			    'sic': {
			        label : mw.msg( 'toolbar-label-spelling' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-sic.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre :  '<sic>',
			                    peri: mw.msg(  'toolbar-peri-spelling' ),
			                    post: '</sic>',
			                }
			        }
			    },

			    'foreign': {
			        label : mw.msg( 'toolbar-label-foreign' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-foreign.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre :  '<foreign>',
			                    peri: mw.msg(  'toolbar-peri-foreign' ),
			                    post: '</foreign>',
			                }
			        }
			    },

			    'ampersand': {
			        label : mw.msg( 'toolbar-label-ampersand' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-ampersand.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre :  '&amp;',
			                    peri: '',
			                    post: '',
			                }
			        }
			    },

			    'longdash': {
			        label : mw.msg( 'toolbar-label-long-dash' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-longdash.png',
			        action: {
			                type: 'encapsulate',
			                options: {
			                    pre : '&#x2014;',
			                    peri: '',
			                    post: '',
			                }
			        }
			    },

			    'commment': {
			        label : mw.msg( 'toolbar-label-comment' ),
			        type  : 'button',
			        icon  : $path + 'jb-button-comment.png',
			        action: {
			                type   : 'encapsulate',
			                options: {
			                    pre:  '<!-- ',
			                    peri: 'toolbar-peri-comment',
			                    post: ' -->',
			                }
			        }
			    }
        }
	} );
};


/* Check if view is in edit mode and that the required modules are available. Then, customize the toolbar . . . */

$(document).ready(function(){
    if( $.inArray( mw.config.get( 'wgAction' ), [ 'edit', 'submit' ] ) !== -1 ) {

        mw.loader.using( 'user.options', function () {
            if ( mw.user.options.get( 'showtoolbar' ) ) {

                if ( mw.user.options.get( 'usebetatoolbar' )) {
                    mw.loader.using( 'ext.wikiEditor.toolbar', function(){
                        $( addExtraButtons );
                    } );
                } else {
                    mw.loader.using( 'mediawiki.action.edit', function(){
                        $( addExtraButtonsToClassicToolBar );
                    } );
                }

                // This must come after the buttons are loaded

                $('#toolbar').append( maxMin );

                $( '#maximise' ).click(function() {
                    $("#mw-content-text").addClass("maximise");
                    $("html, body").animate({ scrollTop: 0 }, "fast");
                });
                $( '#minimise' ).click(function() {
                    $("#mw-content-text").removeClass("maximise");
                });

            }
        } );
    }
}( mediaWiki, jQuery ) );









































