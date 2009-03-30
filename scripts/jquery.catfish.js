/*
 * jQuery Catfish Plugin - Version 1.3
 *
 * Copyright (c) 2007 Matt Oakes (http://www.gizone.co.uk)
 * Licensed under the MIT (LICENSE.txt)
 */

jQuery.fn.catfish = function(options) {
	this.settings = {
		closeLink: 'none',
		animation: 'slide',
		speed: 'normal',
		height: 50,
		limitShow: true,
		showLimit: 1
	}
	if(options)
		jQuery.extend(this.settings, options);
	
	if ( ( this.settings.limitShow == true && ( jQuery.cookie('catman_enable') == null || jQuery.cookie('catman_enable') < this.settings.showLimit ) ) || this.settings.limitShow == false ) {
		
		if ( this.settings.animation != 'slide' && this.settings.animation != 'none' && this.settings.animation != 'fade' ) {
			alert('animation can only be set to \'slide\', \'none\' or \'fade\'');
		}
		
		var id = this.attr('id');
		jQuery(this).css('padding', '0').css('height', this.settings.height + 'px').css('margin', '0').css('width', '100%').css('display', 'none');
		jQuery('html').css('padding', '0 0 ' + ( this.settings.height * 1 + 50 ) + 'px 0');
		if ( typeof document.body.style.maxHeight != "undefined" ) {
			jQuery(this).css('position', 'fixed').css('bottom', '0').css('left', '0');
		}
		
		if ( this.settings.animation == 'slide' ) {
			jQuery(this).slideDown(this.settings.speed);
		}
		else if ( this.settings.animation == 'fade' ) {
			jQuery(this).fadeIn(this.settings.speed);
		}
		else {
			jQuery(this).show();
		}
		if ( this.settings.closeLink != 'none' ) {
			jQuery(this.settings.closeLink).click(function(){
				jQuery.closeCatfish(id);
				return false;
			});
		}
		
		var current_cookie = jQuery.cookie('catman_enable');
		// Set the cookie so it only shows up once per session
		jQuery.cookie('catman_enable', (current_cookie*1) + 1,  {path: '/'});		
		
		// Return jQuery to complete the chain
		return this;
	}
	else {
		jQuery(this).css('display', 'none');
	}
};
jQuery.closeCatfish = function(id) {
	this.catfish = jQuery('#' + id);
	jQuery(this.catfish).hide();
	jQuery('html').css('padding', '0');
	jQuery('body').css('overflow', 'visible'); // Change IE6 hack back
};