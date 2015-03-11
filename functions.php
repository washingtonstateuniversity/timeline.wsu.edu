<?php

class WSU_Timeline_Theme {
	/**
	 * Setup hooks for the theme.
	 */
	public function __construct() {

	}

	public function get_timeline_items() {
		$args = array(
			'post_type' => 'wsu-timeline-point',
			'posts_per_page' => 2000,
		);
		$query = new WP_Query( $args );
		wp_reset_query();
		return $query;
	}
}
$wsu_timeline_theme = new WSU_Timeline_Theme();

function wsu_timeline_get_items() {
	global $wsu_timeline_theme;
	return $wsu_timeline_theme->get_timeline_items();
}