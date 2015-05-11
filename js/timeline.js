/* global Backbone, jQuery, _ */
try{Typekit.load();}catch(e){}

/*! Lazy Load 1.9.5 - MIT license - Copyright 2010-2015 Mika Tuupola */
!function(a,b,c,d){var e=a(b);a.fn.lazyload=function(f){function g(){var b=0;i.each(function(){var c=a(this);if(!j.skip_invisible||c.is(":visible"))if(a.abovethetop(this,j)||a.leftofbegin(this,j));else if(a.belowthefold(this,j)||a.rightoffold(this,j)){if(++b>j.failure_limit)return!1}else c.trigger("appear"),b=0})}var h,i=this,j={threshold:0,failure_limit:0,event:"scroll",effect:"show",container:b,data_attribute:"original",skip_invisible:!1,appear:null,load:null,placeholder:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"};return f&&(d!==f.failurelimit&&(f.failure_limit=f.failurelimit,delete f.failurelimit),d!==f.effectspeed&&(f.effect_speed=f.effectspeed,delete f.effectspeed),a.extend(j,f)),h=j.container===d||j.container===b?e:a(j.container),0===j.event.indexOf("scroll")&&h.bind(j.event,function(){return g()}),this.each(function(){var b=this,c=a(b);b.loaded=!1,(c.attr("src")===d||c.attr("src")===!1)&&c.is("img")&&c.attr("src",j.placeholder),c.one("appear",function(){if(!this.loaded){if(j.appear){var d=i.length;j.appear.call(b,d,j)}a("<img />").bind("load",function(){var d=c.attr("data-"+j.data_attribute);c.hide(),c.is("img")?c.attr("src",d):c.css("background-image","url('"+d+"')"),c[j.effect](j.effect_speed),b.loaded=!0;var e=a.grep(i,function(a){return!a.loaded});if(i=a(e),j.load){var f=i.length;j.load.call(b,f,j)}}).attr("src",c.attr("data-"+j.data_attribute))}}),0!==j.event.indexOf("scroll")&&c.bind(j.event,function(){b.loaded||c.trigger("appear")})}),e.bind("resize",function(){g()}),/(?:iphone|ipod|ipad).*os 5/gi.test(navigator.appVersion)&&e.bind("pageshow",function(b){b.originalEvent&&b.originalEvent.persisted&&i.each(function(){a(this).trigger("appear")})}),a(c).ready(function(){g()}),this},a.belowthefold=function(c,f){var g;return g=f.container===d||f.container===b?(b.innerHeight?b.innerHeight:e.height())+e.scrollTop():a(f.container).offset().top+a(f.container).height(),g<=a(c).offset().top-f.threshold},a.rightoffold=function(c,f){var g;return g=f.container===d||f.container===b?e.width()+e.scrollLeft():a(f.container).offset().left+a(f.container).width(),g<=a(c).offset().left-f.threshold},a.abovethetop=function(c,f){var g;return g=f.container===d||f.container===b?e.scrollTop():a(f.container).offset().top,g>=a(c).offset().top+f.threshold+a(c).height()},a.leftofbegin=function(c,f){var g;return g=f.container===d||f.container===b?e.scrollLeft():a(f.container).offset().left,g>=a(c).offset().left+f.threshold+a(c).width()},a.inviewport=function(b,c){return!(a.rightoffold(b,c)||a.leftofbegin(b,c)||a.belowthefold(b,c)||a.abovethetop(b,c))},a.extend(a.expr[":"],{"below-the-fold":function(b){return a.belowthefold(b,{threshold:0})},"above-the-top":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-screen":function(b){return a.rightoffold(b,{threshold:0})},"left-of-screen":function(b){return!a.rightoffold(b,{threshold:0})},"in-viewport":function(b){return a.inviewport(b,{threshold:0})},"above-the-fold":function(b){return!a.belowthefold(b,{threshold:0})},"right-of-fold":function(b){return a.rightoffold(b,{threshold:0})},"left-of-fold":function(b){return!a.rightoffold(b,{threshold:0})}})}(jQuery,window,document);

var wsuTimeline = wsuTimeline || {};

(function (window, Backbone, $, _, wsuTimeline) {
	'use strict';

	/**
	 * Setup all of the common variables used throughout our scripting.
	 *
	 * @type {boolean}
	 */
	var scrub_is_fixed       = false,
		nav_is_fixed         = false,
		nav_on_display       = false,
		$scrub               = $('.scrub'),
		$scrub_column        = $scrub.find('.column'),
		$scrub_progress_bar  = $('.scrub-progress-bar'),
		$scrub_shade_overlay = $('.scrub-shade-overlay'),
		$home_nav            = $('.wsu-home-navigation'),
		doc_height           = $(document).height(),
		scrub_top            = $scrub.offset().top,
		scrub_width          = $scrub_column.width(),
		scrub_left           = $scrub_column.offset().left,
		timeline_size        = doc_height - scrub_top,
		home_nav_height      = $home_nav.height(),
		decade_markers       = {
			1890 : 0,
			1900 : 0,
			1910 : 0,
			1920 : 0,
			1930 : 0,
			1940 : 0,
			1950 : 0,
			1960 : 0,
			1970 : 0,
			1980 : 0,
			1990 : 0,
			2000 : 0,
			2010 : 0
		},
		current_scroll_top   = 0,
		current_decade_key   = 0,
		current_decade_start = 0,
		current_decade_end   = 0,
		current_decade_perc  = 0;

	wsuTimeline.containerView = Backbone.View.extend({
		el: '.timeline-container',

		events: {
			'click .ti-inside-wrap' : 'clickTimelineItem'
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
			if ( $target.is('.t-content') || $target.parents('.t-content').length ) {
				return;
			}

			// Avoid clicks on the social sharing icons.
			if ( $target.is('.ti-social') || $target.parents('.ti-social').length ) {
				return;
			}

			var $target_parent = $target.parents('.ti-container');

			if ( $target_parent.hasClass('open') ) {
				$target_parent.removeClass('open');
				$target_parent.find('.ti-read-more').html('More');
			} else {
				// Loop through each newly expanded image and assign it's data-src as src.
				$target_parent.find('.t-content-expanded img').each(function(){
					var $current = $(this);
					if ( '' === $current.attr('src') ) {
						$current.attr('src', $current.data('src'));
					}
				});
				$target_parent.find('.ti-feature img').each(function(){
					var $current = $(this);
					if ( '' === $current.attr('src') ) {
						$current.attr('src', $current.data('src'));
					}
				});
				$target_parent.addClass('open');
				$target_parent.find('.ti-read-more').html('Close');
			}
		}

	});

	wsuTimeline.appView = Backbone.View.extend({
		/**
		 * The context in which events are bound.
		 */
		el: '.scrub',

		setup_decade_position: function() {
			var capture_next = false;

			current_scroll_top = $(document).scrollTop();
			for (var k in decade_markers){
				if (decade_markers.hasOwnProperty(k) && 0 !== decade_markers[k]) {
					if ( capture_next ) {
						current_decade_end = decade_markers[k];
						capture_next = false;
					}

					if ( current_scroll_top >= decade_markers[k] ) {
						current_decade_key = k;
						current_decade_start = decade_markers[k];
						capture_next = true;
					}
				}
			}

			var current_decade_total = current_decade_end - current_decade_start;
			var current_decade_marker = current_scroll_top - current_decade_start;

			current_decade_perc = ( current_decade_marker / current_decade_total );
		},

		setup_scrub_position: function() {
			var count_total = 0,
				count_key = 0,
				count_position = false;

			for(var k in decade_markers) {
				if ( decade_markers.hasOwnProperty(k) ) {
					count_total++;
					if ( count_position === false ) {
						if ( k === current_decade_key ) {
							count_position = true;
						} else {
							count_key++;
						}
					}
				}
			}

			var decade_minor = scrub_width / count_total;

			var decade_full = decade_minor * count_key;

			var decade_partial = decade_minor * current_decade_perc;

			var scrub_scroll = Math.floor( scrub_left + decade_full + decade_partial );

			$scrub_progress_bar.width(scrub_scroll);
			$scrub_shade_overlay.css('left', scrub_scroll + 'px');
		},

		setup_decades: function() {
			var self = this;
			for (var k in decade_markers){
				if (decade_markers.hasOwnProperty(k)) {
					var offset = $('.decade-' + k).offset();

					if ( undefined !== offset ) {
						decade_markers[k] = Math.ceil(offset.top);
					}
				}
			}
			self.setup_decade_position();
		},

		initialize: function() {
			if ( 'none' === $home_nav.css('display') ) {
				$home_nav = $('.spine-header');
				this.refreshDefaults();
			}

			this.setup_decades();
			this.setup_decade_position();
			this.setup_scrub_position();
			$(document).scroll(this.scrollTimeline);
			$(document).on('touchmove', this.scrollTimeline);
			$(document).trigger('scroll');
			$(window).on('resize',this.refreshDefaults);
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
			/**
			 * Temporarily position the scrub bar as relative so that we can accurately
			 * calculate the top offset. We then remove the position property so that
			 * the assigned class can handle the behavior.
			 */
			$scrub.css('position','relative');
			scrub_top = $scrub.offset().top;
			$scrub.css('position','');

			scrub_width        = $scrub_column.width();
			scrub_left         = $scrub_column.offset().left;
			doc_height         = $(document).height();
			timeline_size      = doc_height - scrub_top;
			current_scroll_top = $(document).scrollTop();
			home_nav_height    = $home_nav.height();

			if ( undefined !== wsuTimeline.app ) {
				wsuTimeline.app.setup_decades();
				wsuTimeline.app.setup_decade_position();
				wsuTimeline.app.setup_scrub_position();
			}
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
			if ( nav_is_fixed && ! nav_on_display && ( current_scroll_top > scroll_top ) ) {
				$home_nav.css('top',0);
				$scrub.css('top', home_nav_height + 'px');

				nav_on_display = true;
			} else if ( scrub_is_fixed && nav_is_fixed && nav_on_display && ( current_scroll_top < scroll_top ) ) {
				$home_nav.css('top','-' + home_nav_height + 'px');
				$scrub.css('top',0);

				nav_on_display = false;
			}

			// Collect the last scroll point so that we can compare it next time.
			current_scroll_top = scroll_top;

			if ( undefined !== wsuTimeline.app ) {
				wsuTimeline.app.setup_decade_position();
				wsuTimeline.app.setup_scrub_position();
			}
		},

		handleScrub: function(evt){
			var $scrub_mark = $(evt.target);

			if ( ! $scrub_mark.is('.scrub-mark') ) {
				$scrub_mark = $scrub_mark.parents('.scrub-mark');
			}

			var scrub_decade = $scrub_mark.data('decade');

			var closest_year = Math.round(((evt.pageX - $scrub_mark.offset().left) / $scrub_mark.width()) * 10) + scrub_decade;

			var $closest_year_element = $('.item-year-' + closest_year);

			var x = 0;
			while ( 0 === $closest_year_element.length ) {
				// Try going up a year a few times before going backward.
				if ( x <= 4 ) {
					closest_year++;
					x++;
				} else {
					closest_year--;
				}
				$closest_year_element = $('.item-year-' + closest_year);
			}

			var $scroll_top = $closest_year_element.first().offset().top;

			$(window).scrollTop($scroll_top);
		}

	});

	$(document).ready(function() {
		window.wsuTimeline.app = new wsuTimeline.appView();
		window.wsuTimeline.container = new wsuTimeline.containerView();
		$("img.lazy").lazyload();
		$('img.lazy').load(function() {
			window.wsuTimeline.app.refreshDefaults();
		})
	});

	$(window).load(function(){
		if ( '' !== window.location.hash ) {
			var $item_container = $(window.location.hash);
			var $scroll_container = $item_container.find('.ti-inside-wrap');

			if ( 1 === $scroll_container.length ) {
				var scroll_point = $scroll_container.offset().top - 100;
				$(document).scrollTop(scroll_point);
				$scroll_container.trigger('click');
				$item_container.addClass('ti-highlight');
			}
		}
		window.wsuTimeline.app.refreshDefaults();
	});
})(window, Backbone, jQuery, _, wsuTimeline);