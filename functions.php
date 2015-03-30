<?php

// Include the WSU Timeline plugin, which controls some timeline functionality.
include_once( __DIR__ . '/includes/wsu-timeline.php' );

class WSU_Timeline_Theme {
	/**
	 * Setup hooks for the theme.
	 */
	public function __construct() {
		add_action( 'spine_enqueue_styles', array( $this, 'enqueue_styles' ) );
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

	/**
	 * Load the stylesheet from the main wsu.edu theme before the child theme.
	 */
	public function enqueue_styles() {
		wp_dequeue_style( 'spine-theme-child' );
		wp_enqueue_style( 'wsu-home-style', 'https://wsu.edu/wp-content/themes/wsu-home/style.css', array(), spine_get_script_version() );
		wp_enqueue_style( 'spine-theme-child' );
	}
}
$wsu_timeline_theme = new WSU_Timeline_Theme();

function wsu_timeline_get_items() {
	global $wsu_timeline_theme;
	return $wsu_timeline_theme->get_timeline_items();
}