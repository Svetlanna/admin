/**
 * WPGlobus Plus Main
 * Interface JS functions
 *
 * @since 1.0.0
 *
 * @package WPGlobus Plus
 * @subpackage Administration
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusPlus*/
jQuery(document).ready(function($) {
	"use strict";

	$.fn.shake = function(intShakes, intDistance, intDuration) {
		this.each(function() {
			$(this).css('position','relative'); 
			for (var x=1; x<=intShakes; x++) {
				$(this).animate({top:(intDistance*-1)}, (((intDuration/intShakes)/4)))
					.animate({top:intDistance}, ((intDuration/intShakes)/2))
					.animate({top:0}, (((intDuration/intShakes)/4)));
			}
		});
		return this;
	};			
	
	var api =  {
		linkLanguage: { mask:'', value:''},
		linkPostType: { mask:'', value:''},
		init : function() {
			$('#toplevel_page_wpglobus_options .wp-submenu').append('<li><a href="'+WPGlobusPlus.option_page+'">'+WPGlobusPlus.caption_menu_item+'</a></li>');
			this.addListeners();
			this.setModules( 'menu-settings' );
		},
		addListeners : function() {

			if ( 'publish' == WPGlobusPlus.tab ) {
				$('.wpglobus-select').on( 'change', function(ev){
					
					var $t = $( this ), id = $t.attr( 'id' ),
						mask = $t.data( 'mask' ),
						val  = $t.attr( 'value' ),
						link = '';
					
					if ( 'language' == id ) {
						api.linkLanguage.mask  = mask;
						api.linkLanguage.value = val;
					} else if ( 'post_type' == id ) {
						api.linkPostType.mask  = mask;
						api.linkPostType.value = val;
					}
					
					link = WPGlobusPlus.bulk_status_link.replace( api.linkLanguage.mask, api.linkLanguage.value );
					link = link.replace( api.linkPostType.mask, api.linkPostType.value );
						
					$( '.wpglobus-bulk_status_link' ).attr( 'href', link ).text( link ); 

				});
			}
			
			$('.wpglobus-plus-module').on('click', function(ev){
				var $t = $(this), s; 
				api.ajax({
					action: 'activate-module',
					module: $(this).data('module'),
					active_status: $(this).prop('checked') || ''
				}, function(){
					s = $t.parents('.module-block').find('.wpglobus-plus-spinner');
					$t.css({'display':'none'});
					s.css({'display':'block'});
				})
				.done(function (data) {
					api.done( data );
				})
				.fail(function (error) {})
				.always(function (jqXHR, status){
					s.css({'display':'none'});
					$t.css({'display':'inline-block'});
				});				
			});
		},	
		ajax : function(order, beforeSend) {
			return $.ajax({beforeSend:function(){
				if ( typeof beforeSend != 'undefined' ) beforeSend();
			},type:'POST', url:ajaxurl, data:{action:WPGlobusPlus.process_ajax, order:order}, dataType:'json'});
		},	
		setModules: function( module, data ) {
			if ( typeof module == 'string' ) {
				if ( module == 'menu-settings' ) {
					if ( typeof data === 'undefined' ) {
						if ( $('#wpglobus-plus-menu-settings').prop('checked') ) {
							$( '.subtitle-menu-settings' ).css({'display':'block'});
						} else {
							$( '.subtitle-menu-settings' ).css({'display':'none'});
						}							
					} else {
						if ( data.order.active_status == 'true' ) {
							$( '.subtitle-menu-settings' ).css({'display':'block'});
						} else {
							$( '.subtitle-menu-settings' ).css({'display':'none'});
						}	
					}	
				}		
			}
		},	
		done: function( data ) {
			if ( data.order.module == 'menu-settings' ) {
				api.setModules( 'menu-settings', data );
			}	
		}			
	};
	WPGlobusPlus = $.extend({}, WPGlobusPlus, api);
	WPGlobusPlus.init();
});