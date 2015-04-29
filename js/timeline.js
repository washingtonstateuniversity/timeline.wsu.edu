/* global Backbone, jQuery, _ */
try{Typekit.load();}catch(e){}
var wsuTimeline = wsuTimeline || {};

(function (window, Backbone, $, _, wsuTimeline) {
	'use strict';

	/**
	 * Setup all of the common variables used throughout our scripting.
	 *
	 * @type {boolean}
	 */
	var scrub_is_fixed = false,
		nav_is_fixed   = false,
		nav_on_display = false,
		$scrub         = $('.scrub'),
		last_timeline_width = 0,
		$home_nav = $('.wsu-home-navigation'),
		scrub_top = $scrub.offset().top,
		doc_height = $(document).height(),
		timeline_size = doc_height - scrub_top,
		last_scroll_top = $(document).scrollTop(),
		home_nav_height = $home_nav.height();

	wsuTimeline.containerView = Backbone.View.extend({
		el: '.timeline-container',

		events: {
			'click .timeline-item-internal-wrapper' : 'clickTimelineItem'
		},

		/**
		 * Handle click events on individual timeline items.
		 *
		 * - Items should expand and retract depending on their current state.
		 * - Read more text should swap between "More" and "Close"
		 * - We should avoid clicks on the actual content of an item.
		 * - We should avoid clicks on social icons.
		 *
		 * @param evt
		 */
		clickTimelineItem: function(evt) {
			var $target = $(evt.target);

			// Avoid clicks on the content itself. Prevents confusion when copying text.
			if ( $target.is('.timeline-content') || $target.parents('.timeline-content').length ) {
				return;
			}

			// Avoid clicks on the social sharing icons.
			if ( $target.is('.timeline-item-social') || $target.parents('.timeline-item-social').length ) {
				return;
			}

			var $target_parent = $target.parents('.timeline-item-container');

			if ( $target_parent.hasClass('open') ) {
				$target_parent.removeClass('open');
				$target_parent.find('.timeline-item-read-more').html('More');
			} else {
				// Loop through each newly expanded image and assign it's data-src as src.
				$target_parent.find('.timeline-content-expanded img').each(function(){
					var $current = $(this);
					if ( '' === $current.attr('src') ) {
						$current.attr('src', $current.data('src'));
					}
				});
				$target_parent.addClass('open');
				$target_parent.find('.timeline-item-read-more').html('Close');
			}
		}

	});

	wsuTimeline.appView = Backbone.View.extend({
		/**
		 * The context in which events are bound.
		 */
		el: '.scrub',

		initialize: function() {
			$(document).scroll(this.scrollTimeline);
			$(document).trigger('scroll');
			$(document).on('resize',this.refreshDefaults);
		},

		// Setup the events used in the overall application view.
		events: {
			'click .scrub-mark': 'handleScrub'
		},

		/**
		 * Setup our original variables to match the properties of
		 * the current document after a resize event has fired.
		 */
		refreshDefaults: function() {
			scrub_top = $scrub.offset().top;
			doc_height = $(document).height();
			timeline_size = doc_height - scrub_top;
			last_scroll_top = $(document).scrollTop();
			home_nav_height = $home_nav.height();
			$(document).trigger('scroll');
		},

		/**
		 * Manage the scroll event attached to the timeline page. This primarily handles the
		 * navigation area and the timeline scrubber area. At times none, one, or both are
		 * in a fixed position.
		 */
		scrollTimeline: function() {
			/**
			 * Determine our current document position.
			 */
			var scroll_top = $(document).scrollTop();

			/**
			 * When scrolling, we check for the position at which the nav menu and scrubber
			 * need to transition between fixed and relative positioning. If the nav is fixed,
			 * our marker is different than if the nav is off screen.
			 *
			 * @type {number}
			 */
			var scroll_marker = 0;
			if ( nav_is_fixed && nav_on_display ) {
				scroll_marker = home_nav_height;
			}

			/**
			 * Control the transition at the top of the document when the scrubber is deciding
			 * to become a fixed element and the navigation is deciding whether to remain fixed
			 * or retreat to the safety of a standard element.
			 *
			 * - If the scrubber is not yet fixed and its original position in the document passes
			 *   the top of our scroll area, then convert it to a fixed element and assign a top
			 *   value of that scroll area's marker.
			 *
			 * - If the scrubber is already fixed and we scroll up into the header area where
			 *   the scrubber needs to be unattached, then clear its fixed properties. At the same time,
			 *   ensure the margin of the page is set to match so that other elements align properly.
			 *   This alignment is still shaky.
			 *
			 * - If the scrubber is not fixed, but nudges against the fixed navigation and reaches a
			 *   point where it should be fixed, then re-attach it and hide the navigation area.
			 */
			if ( ! scrub_is_fixed && ( scrub_top - scroll_top ) <= scroll_marker && 0 === scroll_marker ) {
				$scrub.addClass('scrub-fixed').css('top',scroll_marker + 'px');
				scrub_is_fixed = true;

				/**
				 * If the nav is not yet fixed when we reach this position, set it as fixed with
				 * a top position of it's negative height. This will put it right off screen so
				 * that it can nudge into view when we scroll down.
				 */
				if ( ! nav_is_fixed ) {
					$home_nav.addClass('nav-fixed').css('top','-' + home_nav_height + 'px');
					nav_is_fixed = true;
				}
			} else if ( scrub_is_fixed && 0 !== scroll_marker && ( scrub_top - scroll_top ) > scroll_marker ) {
				$scrub.removeClass('scrub-fixed').css('top',0);
				$('main').css('margin-top', home_nav_height + 'px' );

				scrub_is_fixed = false;
			} else if ( ! scrub_is_fixed && nav_is_fixed && 0 !== scroll_marker && ( scrub_top - scroll_top ) <= scroll_marker ) {
				$scrub.addClass('scrub-fixed').css('top', 0);
				$home_nav.css('top','-' + home_nav_height + 'px');

				scrub_is_fixed = true;
				nav_on_display = false;
			}

			/**
			 * If navigation is already fixed, but hidden and we know that we're starting to
			 * scroll back up the page, bring the navigation back into view.
			 *
			 * If the navigation is fixed, displayed, and we know that we're starting to scroll
			 * back down the page, hide the navigation from view.
			 */
			if ( nav_is_fixed && ! nav_on_display && ( last_scroll_top > scroll_top ) ) {
				$home_nav.css('top',0);
				$scrub.css('top', home_nav_height + 'px');

				nav_on_display = true;
			} else if ( scrub_is_fixed && nav_is_fixed && nav_on_display && ( last_scroll_top < scroll_top ) ) {
				$home_nav.css('top','-' + home_nav_height + 'px');
				$scrub.css('top',0);

				nav_on_display = false;
			}

			// Collect the last scroll point so that we can compare it next time.
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
		},

		handleScrub: function(evt){
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
			var $decades = $first_year.parent('.decade').prevAll('.decade');
			var $centuries = $first_year.parent('.decade').parent('.century').prevAll('.century');

			$centuries.hide();
			$decades.hide();
			$decade_items.hide();

			$('.scrub-shade-overlay').css('width',evt.pageX + 'px');
			console.log(closest_year, scrub_decade);
		}

	});

	$(document).ready(function(){
		window.wsuTimeline.app = new wsuTimeline.appView();
		window.wsuTimeline.container = new wsuTimeline.containerView();
	});
})(window, Backbone, jQuery, _, wsuTimeline);