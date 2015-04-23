<?php

// Include the WSU Timeline plugin, which controls some timeline functionality.
include_once( __DIR__ . '/includes/wsu-timeline.php' );

class WSU_Timeline_Theme {
	/**
	 * @var string Theme version for cache breaking.
	 */
	public static $version = '0.6.2';

	/**
	 * Setup hooks for the theme.
	 */
	public function __construct() {
		add_action( 'spine_enqueue_styles', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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

	/**
	 * Serve a cached version of the nav menu if it exists. Cache nav menus as
	 * they are generated for future immediate use.
	 *
	 * @param array $menu_args List of arguments for the menu. Used for the menu and the cache key.
	 *
	 * @return string HTML output for the menu.
	 */
	public function get_menu( $menu_args ) {
		$cache_incr_key = md5( $menu_args['theme_location'] );
		$cache_key = md5( serialize( $menu_args ) );

		if ( ! $cache_incr = wp_cache_get( $cache_incr_key, 'wsu-home-nav' ) ) {
			$cache_incr = '';
		}

		if ( $nav_menu = wp_cache_get( $cache_key . $cache_incr, 'wsu-home-nav' ) ) {
			return $nav_menu;
		}

		ob_start();
		wp_nav_menu( $menu_args );
		$nav_menu = ob_get_contents();
		ob_end_clean();

		wp_cache_set( $cache_key . $cache_incr, $nav_menu, 'wsu-home-nav', 3600 );

		return $nav_menu;
	}
}
$wsu_timeline_theme = new WSU_Timeline_Theme();

/**
 * Retrieve the HTML for a nav menu.
 *
 * @param $menu_args
 *
 * @return string
 */
function wsu_timeline_get_menu( $menu_args ) {
	global $wsu_timeline_theme;
	return $wsu_timeline_theme->get_menu( $menu_args );
}