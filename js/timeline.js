try{Typekit.load();}catch(e){}

(function($,window){

	$(document).ready(function(){
		var $scrub = $('.scrub');
		var scrub_top = $scrub.offset().top;
		var doc_height = $(document).height();
		var timeline_size = doc_height - scrub_top;
		var last_timeline_width = 0;

		$(document).scroll(function(e){
			var scroll_top = $(document).scrollTop();

			if ( ( scrub_top - scroll_top ) <= 0 && ! $scrub.hasClass('scrub-fixed') ) {
				$scrub.addClass('scrub-fixed');
			} else if ( ( scrub_top - scroll_top ) > 0 && $scrub.hasClass('scrub-fixed') ) {
				$scrub.removeClass('scrub-fixed');
			}

			var timeline_width = ( scroll_top / timeline_size ) * 100;
			timeline_width = timeline_width.toFixed(4) / 1;

			if ( timeline_width !== last_timeline_width ) {
				last_timeline_width = timeline_width;
				jQuery('.scrub-progress-bar').css('width', timeline_width + '%' );
			}

		});
	});
}(jQuery));