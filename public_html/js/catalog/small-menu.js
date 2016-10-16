jQuery( document ).ready( function( $ ) {
	var $masthead = $( '.site-header' ),
	    timeout = false;

	$.fn.smallMenu = function() {
		$masthead.find( '.sub-menu' ).removeClass( 'main-navigation' ).addClass( 'main-small-navigation' );
		$masthead.find( '.sub-menu h1' ).removeClass( 'assistive-text' ).addClass( 'menu-toggle' );

		$( '.menu-toggle' ).unbind( 'click' ).click( function() {
			$masthead.find( '.sub-menu-list' ).toggle();
			$( this ).toggleClass( 'toggled-on' );
		} );
	};

	// Check viewport width on first load.
	if ( $( window ).width() < 660 )
		$.fn.smallMenu();

	// Check viewport width when user resizes the browser window.
	$( window ).resize( function() {
		var browserWidth = $( window ).width();

		if ( false !== timeout )
			clearTimeout( timeout );

		timeout = setTimeout( function() {
			if ( browserWidth < 660 ) {
				$.fn.smallMenu();
			} else {
				$masthead.find( '.sub-menu' ).removeClass( 'main-small-navigation' ).addClass( 'main-navigation' );
				$masthead.find( '.sub-menu h1' ).removeClass( 'menu-toggle' ).addClass( 'assistive-text' );
				$masthead.find( '.sub-menu-list' ).removeAttr( 'style' );
			}
		}, 200 );
	} );
} );