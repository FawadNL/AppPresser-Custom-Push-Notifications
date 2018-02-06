// Add custom ajax powered links to your AppPresser app. Add this to a javascript file, enqueued in your main WP Child Theme

var AppCustom = {};

AppCustom.init = function() {

	$ = jQuery;

	$('body').on('click', 'a.test-link, #another-link a', function(event) {
	
		if ( window.apppresser.canAjax( $(this) ) ) {
			window.apppresser.loadAjaxContent( $(this).attr('href'), false, event );
		}
	});
}

// load script on doc ready and ajax content load. Don't put everything in a .ready function, it won't load properly.
jQuery(document).ready( AppCustom.init ).on( 'load_ajax_content_done', AppCustom.init );
