try{Typekit.load();}catch(e){}

(function($,window){

	$(document).ready(function(){
		var $scrub = $('.scrub');
		var scrub_top = $scrub.offset().top;

		$(document).scroll(function(e){
			var scroll_top = $(document).scrollTop();

			if ( ( scrub_top - scroll_top ) <= 0 && ! $scrub.hasClass('scrub-fixed') ) {
				$scrub.addClass('scrub-fixed');
			} else if ( ( scrub_top - scroll_top ) > 0 && $scrub.hasClass('scrub-fixed') ) {
				$scrub.removeClass('scrub-fixed');
			}
		});
	});
}(jQuery));