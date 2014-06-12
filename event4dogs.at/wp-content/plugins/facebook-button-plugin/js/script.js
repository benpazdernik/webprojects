(function($) {
	$(document).ready( function() {
		$( '#fcbkbttn_settings_form input' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#fcbkbttn_settings_notice' ).css( 'display', 'block' );
			};
		});
		$( '#fcbkbttn_settings_form select' ).bind( "change", function() {
			$( '.updated.fade' ).css( 'display', 'none' );
			$( '#fcbkbttn_settings_notice' ).css( 'display', 'block' );
		});
	});
})(jQuery);