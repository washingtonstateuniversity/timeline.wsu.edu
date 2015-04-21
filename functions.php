<?php

// Include the WSU Timeline plugin, which controls some timeline functionality.
include_once( __DIR__ . '/includes/wsu-timeline.php' );

class WSU_Timeline_Theme {
	/**
	 * @var string Theme version for cache breaking.
	 */
	public static $version = '0.5.0';

	/**
	 * Setup hooks for the theme.
	 */
	public function __construct() {
		add_action( 'spine_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Retrieve a list timeline items.
	 *
	 * @return WP_Query
	 */
	public function get_timeline_items() {
		$args = array(
			'post_type' => 'wsu-timeline-point',
			'posts_per_page' => 2000,
			'order'     => 'ASC',
			'meta_key' => '_wsu_tp_start_date',
			'orderby'   => 'meta_value_num',
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

	/**
	 * Enqueue the scripts used in the theme.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'wsu-home-typekit', 'https://use.typekit.net/roi0hte.js', array(), false, false );
		wp_enqueue_script( 'wsu-timeline', get_stylesheet_directory_uri() . '/js/timeline.js', array( 'jquery' ), spine_get_script_version(), true );
	}
}
$wsu_timeline_theme = new WSU_Timeline_Theme();

/**
 * Wrapper to retrieve a list of timeline items.
 *
 * @return WP_Query
 */
function wsu_timeline_get_items() {
	global $wsu_timeline_theme;
	return $wsu_timeline_theme->get_timeline_items();
}