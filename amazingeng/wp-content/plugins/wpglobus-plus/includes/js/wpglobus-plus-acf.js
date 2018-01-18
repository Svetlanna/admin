/*jslint browser: true*/
/*global jQuery, console, WPGlobusCore, WPGlobusCoreData, WPGlobusPlusAcf*/
(function($) {
	"use strict";
	
	if ( typeof WPGlobusCoreData === 'undefined' ) {
		return;	
	}
	
	if ( typeof WPGlobusPlusAcf === 'undefined' ) {
		return;	
	}	
	
	var api = {	
		option : {
			language: WPGlobusCoreData.default_language,
			content: '',
			activeClass: 'mce-active'
		},
		language: {},
		content: {},
		acfPro: false,
		savedPost: false,
		init: function(args) {
			api.option = $.extend( api.option, args );
			if ( typeof WPGlobusAcf != 'undefined' ) {
				api.acfPro = WPGlobusAcf.pro;
			}	
			api.addButtons();
			
			if ( api.acfPro ) {

				$(document).on( 'wpglobus_before_save_post', function(e, args) {
					if ( api.savedPost ) {
						return;	
					}	
					api.savedPost = true;
					$.each( api.content, function( id, content ) {
						if ( tinymce.get( id ) == null || tinymce.get( id ).isHidden() ) {
							$( '#' + id ).val( content );
							$( '#' + id + '-tmce' ).click();
						} else {
							tinymce.get( id ).setContent( content, { format:'raw' } );
						}	

					});				
				});
			} else {	
				api.setValidation();
				
				$(document).on( 'wpglobus_before_save_post', function(e, args) {
					/* @see acf input.js file */
					if( ! acf.validation.disabled )
					{
						// do validation
						acf.validation.run();				
					}
					if( acf.validation.status ) {
						$.each(api.content, function(id,content){
							tinymce.get(id).setContent( content );
						});
					}	
				});
			}
			api.editorsInit();
		},
		update: function( event ) {
				
			var id, text;

			if ( typeof event.target !== 'undefined' ) {
				id = event.target.id;
			} else {
				return;	
			}
			
			if ( id == 'tinymce' ) {
				id = event.target.dataset.id;	
			}	

			if ( typeof api.content[ id ] === 'undefined' ) {
				return;
			}

			if ( tinymce.get( id ) == null || tinymce.get( id ).isHidden() ) {
				text = $( '#' + id ).val();
			} else {
				text = tinymce.get( id ).getContent( { format: 'raw' } );
			}	
			api.content[ id ] = WPGlobusCore.getString( api.content[ id ], text, api.language[ id ] );
			
		},	
		editorsInit: function() {
			if ( ! api.acfPro ) {
				return;	
			}	
			$( document ).on( 'tinymce-editor-init', function( event, editor ) {
			
				if ( -1 == editor.id.indexOf( 'acf-editor-' ) ) {
					return;
				}
				
				/** tinymce */
				editor.on( 'nodechange keyup', _.debounce( api.update, 1000 ) );
				
				/** textarea */
				$( '#' + editor.id ).on( 'input keyup', _.debounce( api.update, 1000 ) );
				
			} );
		},	
		setValidation: function() {
			/* @see acf input.js file */
			if ( acf.validation.disabled ) {
				return;	
			}
			
			$(document).on('acf/validate_field', function(ev, div){
				var $div = $( div ), id, source = '', 
					valid = true;

				if ( $div.hasClass('field_type-textarea') ) {
					$div.data('validation',false);
					id = $div.find('textarea').attr('id');
					source = $('#'+id).val();
				} else if ( $div.hasClass('field_type-text') ) {
					$div.data('validation',false);
					id = $div.find('input').attr('id');
					source = $('#'+id).val();
				} else {
					// don't validate other fields in version 1.0.0
					return;	
				}	
				if ( '' == source ) {
					return;
				}
				source = WPGlobusCore.getTranslations( source );
				
				$.each( WPGlobusCoreData.enabled_languages, function(i,l) {
					if ( '' == source[l] ) {
						valid = false;
						return false;
					}
					valid = true;		
				});
			
				if ( valid ) {
					$div.data('validation',true);
					$('#wpglobus-dialog-start-'+id).removeClass('wpglobus_dialog_error');
				} else {
					$('#wpglobus-dialog-start-'+id).addClass('wpglobus_dialog_error').shake(3,7,800);
				}	
				return;
			});
		},		
		saveContent: function( editor, language ) {
			var c;
			if ( WPGlobusPlusAcf.removeEmptyP ) {
				c = editor.getContent().replace(/<p>\s*<\/p>/g, '' ); // remove empty p
			} else {
				c = editor.getContent();
			}	
			api.content[editor.id] = WPGlobusCore.getString( api.content[editor.id], c, language );
		},
		getTranslation: function( editor, language ) {
			return WPGlobusCore.getTranslations( api.content[editor.id] )[language];	
		},
		removeClass: function( id ) {
			$('.mce-wpglobus-plus-acf-button-'+id).removeClass( api.option.activeClass );
		},
		initContent: function( editor ) {
			var c  = $('#'+editor.id).text(),
				tr = WPGlobusCore.getTranslations( c );
			
			$.each(tr, function(l,e){
				if ( l !== WPGlobusCoreData.default_language ) {
					// convert double '\n' to couple tags p for extra language
					var a = tr[l].split('\n\n');
					$.each(a, function(i,el){
						if ( el !== '' ) {
							a[i] = '<p>'+el+'</p>';	
						}	
					});
					tr[l] = a.join('');	
					c = WPGlobusCore.getString( c, tr[l], l );
				}	
			});
			api.content[editor.id]  = c;
		},	
		addButtons: function() {
			tinymce.PluginManager.add( 'wpglobus_plus_acf_separator', function( editor, url ) {
				editor.addButton( 'wpglobus_plus_acf_separator', {
					text: '',
					icon: 'wpglobus-plus-globe'
				});			
			});
			$.each( WPGlobusCoreData.enabled_languages, function(i,language) {	
				tinymce.PluginManager.add('wpglobus_plus_acf_button_'+language, function( editor, url ) {
					var classes = 'widget btn wpglobus-plus-acf-button',
						active_class = '';
					// 'wysiwyg-acf-field' - acf
					// 'acf-editor-' - acf pro
					if ( editor.id.indexOf('wysiwyg-acf-field') >= 0 || editor.id.indexOf('acf-editor-') >= 0 ) {

						if ( language == WPGlobusCoreData.default_language ) {
							
							/**
							 * add WPGlobus translatable class
							 */
							_.delay( function () {
								$( editor.iframeElement ).addClass( 'wpglobus-translatable' ).css({'width':'99%'});							
								$( '#' + editor.id ).addClass( 'wpglobus-translatable' );							
							}, 2000 ); 

							/**
							 * Init
							 */	
							api.initContent( editor );
							api.language[editor.id] = api.option.language;
							$('#'+editor.id).val( api.getTranslation(editor,language) );
							active_class = ' active';
							
							editor.on('blur', function(event,l){
								api.saveContent( editor, api.language[editor.id] );
							});								
							
						}	
					}	

					editor.addButton('wpglobus_plus_acf_button_'+language, {
						text: WPGlobusCoreData.en_language_name[language],
						icon: false,
						tooltip: 'Select '+WPGlobusCoreData.en_language_name[language]+' language',
						value: language,
						classes: classes + active_class + ' wpglobus-plus-acf-button-'+language + ' wpglobus-plus-acf-button-'+editor.id,
						onclick: function() {
							var t = $( this ),
								id = t[0]['_id'],
								l = WPGlobusCoreData.default_language;
							
							if ( typeof t[0]['_value'] != 'undefined' ) {
								l = t[0]['_value'];
							} else if ( typeof t[0].settings.value != 'undefined' ) {
								l = t[0].settings.value;
							} else {
								console.log('Language value not defined. It was set to default.');	
							}
							
							api.removeClass( editor.id );
							$('#'+id).addClass( api.option.activeClass );
							api.saveContent( editor, api.language[editor.id] );
							api.language[editor.id] = l;
							editor.setContent( api.getTranslation( editor, api.language[editor.id] ) );
						}	
					});
				});	
				
			});

		}	
	
	}
	
	WPGlobusPlusAcf = $.extend({}, WPGlobusPlusAcf, api);
	
	WPGlobusPlusAcf.init();
	
})(jQuery);