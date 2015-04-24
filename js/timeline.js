try{Typekit.load();}catch(e){}

(function($,window){

	$(document).ready(function(){
		var $scrub = $('.scrub'),
			$main_timeline = $('.timeline-container'),
			scrub_top = $scrub.offset().top,
			doc_height = $(document).height(),
			timeline_size = doc_height - scrub_top,
			last_timeline_width = 0,
			last_scroll_top = $(document).scrollTop(),
			$home_nav = $('.wsu-home-navigation'),
			home_nav_height = $home_nav.height(),
			scrub_is_fixed = false,
			nav_is_fixed = true;

		$(document).scroll(function(e){
			var scroll_top = $(document).scrollTop();
			var scroll_marker = 0;

			if ( nav_is_fixed ) {
				scroll_marker = home_nav_height;
			}

			if ( ( scrub_top - scroll_top ) <= scroll_marker && ! $scrub.hasClass('scrub-fixed') ) {
				$scrub.addClass('scrub-fixed');
			} else if ( ( scrub_top - scroll_top ) > scroll_marker && $scrub.hasClass('scrub-fixed') ) {
				$scrub.removeClass('scrub-fixed');
			}

			/**
			 * - If the scrub area has hit the top of the scroll
			 * - And we're scrolling up
			 * - And the home nav does not have a fixed class
			 * - Then add the fixed class
			 */
			if ( ( scrub_top - scroll_top ) <= 0 && scroll_top < last_scroll_top && ! $home_nav.hasClass('nav-fixed') ) {
				$home_nav.addClass('nav-fixed');
				nav_is_fixed = true;
				$scrub.css('top', home_nav_height + 'px' );
				$main_timeline.css('margin-top', home_nav_height + 'px' );
			} else if ( ( scrub_top - scroll_top ) <= 0 && scroll_top > last_scroll_top && $home_nav.hasClass('nav-fixed') ) {
				$home_nav.removeClass('nav-fixed');
				nav_is_fixed = false;
				$scrub.css('top', 0 );
				$main_timeline.css('margin-top',0);
			}

			last_scroll_top = scroll_top;

			/**
			 * Draw a progress bar based on the total height of the document. This will
			 * be refactored to watch the actual progression of time.
			 */
			var timeline_width = ( scroll_top / timeline_size ) * 100;
			timeline_width = timeline_width.toFixed(4) / 1;

			if ( timeline_width !== last_timeline_width ) {
				last_timeline_width = timeline_width;
				jQuery('.scrub-progress-bar').css('width', timeline_width + '%' );
			}
		});
		$(document).trigger('scroll');

		$scrub.on('click', '.scrub-mark', function(evt){

			var $scrub_mark = $(evt.target);

			if ( ! $scrub_mark.is('.scrub-mark') ) {
				$scrub_mark = $scrub_mark.parents('.scrub-mark');
			}

			var scrub_decade = $scrub_mark.data('decade');

			var closest_year = Math.round(((evt.pageX - $scrub_mark.offset().left) / $scrub_mark.width()) * 10) + scrub_decade;

			$('.timeline-item-container').show();
			$('.decade').show();
			$('.century').show();

			var $closest_year_element = $('.item-year-' + closest_year);

			while ( 0 === $closest_year_element.length ) {
				closest_year++;
				$closest_year_element = $('.item-year-' + closest_year);
			}

			var $first_year = $closest_year_element.first();

			var $decade_items = $first_year.prevAll('.timeline-item-container');
			var $decades = $first_year.parent('.decade').prevAll('.decade')
			var $centuries = $first_year.parent('.decade').parent('.century').prevAll('.century');

			$centuries.hide();
			$decades.hide();
			$decade_items.hide();

			console.log(closest_year, scrub_decade);
		});

		//$('.item-year-1917').first().prevAll('.timeline-item-container').hide().parent('.decade').prevAll('.decade').hide().parent('.century').prevAll('.century').hide();
	});
}(jQuery));