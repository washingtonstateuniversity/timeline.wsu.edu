<?php

/**
 * Class WSU_Timeline
 */
class WSU_Timeline {
	/**
	 * @var string Slug used for the timeline point content type.
	 */
	var $point_content_type_slug = 'wsu-timeline-point';

	/**
	 * @var string Slug used for the timeline decade content type.
	 */
	var $decade_content_type_slug = 'wsu-timeline-decade';

	/**
	 * Setup plugin.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
		add_action( 'init', array( $this, 'register_content_types' ), 10 );
		add_action( 'init', array( $this, 'setup_custom_taxonomies' ), 99 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'manage_' . $this->point_content_type_slug . '_posts_columns', array( $this, 'manage_item_posts_columns' ), 10, 1 );
		add_action( 'manage_' . $this->point_content_type_slug . '_posts_custom_column', array( $this, 'manage_item_posts_custom_column' ), 10, 2 );

		add_filter( 'enter_title_here', array( $this, 'modify_enter_title_text' ), 10, 2 );
	}

	/**
	 * Enqueue the scripts and styles used by the plugin in the admin.
	 */
	public function admin_enqueue_scripts() {
		if ( ! is_admin() || get_current_screen()->id !== $this->point_content_type_slug ) {
			return;
		}

		wp_enqueue_style( 'jquery-ui-styles', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.min.css' );
		wp_enqueue_style( 'wsu-tp-admin-styles', get_stylesheet_directory_uri() . '/css/admin.css', array(), WSU_Timeline_Theme::$version );
		wp_enqueue_script( 'wsu-tp-admin-scripts', get_stylesheet_directory_uri() . '/js/admin.js', array( 'jquery-ui-datepicker' ), WSU_Timeline_Theme::$version, true );
	}

	/**
	 * Register the content types used for displaying information on the timeline.
	 */
	public function register_content_types() {
		$labels = array(
			'name'               => __( 'Timeline Items', 'wsuwp_uc' ),
			'singular_name'      => __( 'Timeline Item', 'wsuwp_uc' ),
			'all_items'          => __( 'All Timeline Items', 'wsuwp_uc' ),
			'add_new_item'       => __( 'Add Timeline Item', 'wsuwp_uc' ),
			'edit_item'          => __( 'Edit Timeline Item', 'wsuwp_uc' ),
			'new_item'           => __( 'New Timeline Item', 'wsuwp_uc' ),
			'view_item'          => __( 'View Timeline Item', 'wsuwp_uc' ),
			'search_items'       => __( 'Search Timeline Items', 'wsuwp_uc' ),
			'not_found'          => __( 'No Timeline Items found', 'wsuwp_uc' ),
			'not_found_in_trash' => __( 'No Timeline Items found in trash', 'wsuwp_uc' ),
		);

		$args = array(
			'labels' => $labels,
			'description' => 'Points on the WSU Timeline',
			'public' => true,
			'hierarchical' => false,
			'menu_icon' => 'dashicons-location-alt',
			'supports' => array (
				'title',
				'editor',
				'revisions',
				'thumbnail',
			),
			'taxonomies' => array(
				'category',
				'post_tag',
			),
			'has_archive' => false,
			'rewrite' => array(
				'slug' => 'timeline',
			)
		);
		register_post_type( $this->point_content_type_slug, $args );

		$labels = array(
			'name'               => 'Timeline Decades',
			'singular_name'      => 'Timeline Decade',
			'all_items'          => 'All Timeline Decades',
			'add_new_item'       => 'Add New Decade',
			'edit_item'          => 'Edit Decade',
			'new_item'           => 'New Decade',
			'view_item'          => 'View Decade',
			'search_item'        => 'Search Decades',
			'not_found'          => 'No decades found',
			'not_found_in_trash' => 'No decades found in trash',
		);

		$args = array(
			'labels' => $labels,
			'description' => 'Decades in the WSU Timeline',
			'public' => false,
			'show_ui' => true,
			'hieararchical' => false,
			'menu_icon' => 'dashicons-calendar',
			'supports' => array(
				'title',
				'editor',
				'revisions',
				'thumbnail',
			),
			'has_archive' => false,
		);
		register_post_type( $this->decade_content_type_slug, $args );
	}

	/**
	 * Explicitly register support for our University taxonomies.
	 */
	public function setup_custom_taxonomies() {
		register_taxonomy_for_object_type( 'wsuwp_university_category', $this->point_content_type_slug );
		register_taxonomy_for_object_type( 'wsuwp_university_location', $this->point_content_type_slug );
	}

	/**
	 * Add meta boxes to be used with this content type.
	 *
	 * @param string $post_type The post type page being displayed.
	 */
	public function add_meta_boxes( $post_type ) {
		global $_wp_post_type_features;

		if ( $this->point_content_type_slug !== $post_type ) {
			return;
		}

		// Remove the default editor box, we'll take care of this on our own.
		unset( $_wp_post_type_features[ $this->point_content_type_slug ]['editor'] );

		add_meta_box( 'wsu-timeline-point-data', 'Timeline Item Data', array( $this, 'display_timeline_point_meta_box' ), $this->point_content_type_slug, 'normal', 'high' );
		add_meta_box( 'wsu-timeline-submitter-data', 'Item Submission Information', array( $this, 'display_timeline_item_submitter_meta_box' ), $this->point_content_type_slug, 'normal', 'default' );
	}

	/**
	 * Display a meta box to capture the various data points required for a timeline point.
	 */
	public function display_timeline_point_meta_box( $post ) {
		$sub_headline = get_post_meta( $post->ID, '_wsu_tp_sub_headline', true );
		$start_date = get_post_meta( $post->ID, '_wsu_tp_start_date', true );
		$end_date = get_post_meta( $post->ID, '_wsu_tp_end_date', true );
		$external_url = get_post_meta( $post->ID, '_wsu_tp_external_url', true );
		$video_url = get_post_meta( $post->ID, '_wsu_tp_video_url', true );
		$submitter_source = get_post_meta( $post->ID, '_wsu_tp_story_source', true );

		$start_date = $this->string_date_to_slash( $start_date );
		$end_date = $this->string_date_to_slash( $end_date );

		wp_nonce_field( 'wsu-timeline-save-point', '_wsu_timeline_point_nonce' );
		?>
		<div class="capture-point-data">
			<div class="capture-section capture-sub-headline">
				<label for="wsu-tp-sub-headline">Sub headline:</label>
				<input type="text" id="wsu-tp-sub-headline" name="wsu_tp_sub_headline" value="<?php echo esc_attr( $sub_headline ); ?>" />
				<p class="description">This will provide additional context to the primary headline above.</p>
			</div>

			<div class="capture-section">
				<div class="capture-start-date">
					<label for="wsu-tp-start-date" class="datepicker-label">Start Date:</label>
					<input type="text" id="wsu-tp-start-date" name="wsu_tp_start_date" class="datepicker" value="<?php echo esc_attr( $start_date ); ?>" />
				</div>

				<div class="capture-end-date">
					<label for="wsu-tp-end-date" class="datepicker-label">End Date:</label>
					<input type="text" id="wsu-tp-end-date" name="wsu_tp_end_date" class="datepicker" value="<?php echo esc_attr( $end_date ); ?>" />
				</div>
				<p class="description">Use the first of the month or first of the year if a specific date is not available.</p>
			</div>

			<div class="capture-section capture-video-url">
				<label for="wsu-tp-video-url">Video URL:</label>
				<input type="text" id="wsu-tp-video-url" name="wsu_tp_video_url" value="<?php echo esc_attr( $video_url ); ?>" />
				<p class="description">This URL may be used to embed a video in the timeline. YouTube is preferred, but we may be able to do something in the future with others.</p>
			</div>

			<div class="capture-section capture-external-url">
				<label for="wsu-tp-external-url">External URL:</label>
				<input type="text" id="wsu-tp-external-url" name="wsu_tp_external_url" value="<?php echo esc_attr( $external_url ); ?>" />
				<p class="description">This URL will be displayed publicly to provide a route to external information.</p>
			</div>

			<div class="capture-section capture-story-source">
				<label for="wsu-tp-story-source">Background Information/Notes:</label>
				<p class="description">These notes will not be displayed in the public view for a timeline item and can be used as part of the editorial process.</p>
				<textarea id="wsu-tp-story-source" name="wsu_tp_story_source"><?php echo esc_textarea( $submitter_source ); ?></textarea>
			</div>

			<div class="clear"></div>
		</div>

		<h4 id="content-description">Timeline Item Description:</h4>
		<p id="content-description-description" class="description">This is displayed publicly as the main content for the timeline item.</p>
		<?php

		// Add the default WP Editor below other fields we're capturing.
		wp_editor( $post->post_content, 'content' );
	}

	public function display_timeline_item_submitter_meta_box( $post ) {
		$submitter_name = get_post_meta( $post->ID, '_wsu_tp_submitter_name', true );
		$submitter_email = get_post_meta( $post->ID, '_wsu_tp_submitter_email', true );
		$submitter_phone = get_post_meta( $post->ID, '_wsu_tp_submitter_phone', true );

		?>
		<div class="capture-point-data">
			<label for="wsu-tp-submitter-name">Submitter Name:</label>
			<input type="text" id="wsu-tp-submitter-name" name="wsu_tp_submitter_name" value="<?php echo esc_attr( $submitter_name ); ?>" />

			<label for="wsu-tp-submitter-email">Submitter Email:</label>
			<input type="text" id="wsu-tp-submitter-email" name="wsu_tp_submitter_email" value="<?php echo esc_attr( $submitter_email ); ?>" />

			<label for="wsu-tp-submitter-phone">Submitter Phone:</label>
			<input type="text" id="wsu-tp-submitter-phone" name="wsu_tp_submitter_phone" value="<?php echo esc_attr( $submitter_phone ); ?>" />

			<div class="clear"></div>
		</div>
	<?php
	}

	/**
	 * Turn a string of YYYYMMDD and turn it into a date string separated
	 * by "/" for input and readability in the admin.
	 *
	 * @param string $date
	 *
	 * @return bool|string
	 */
	public function string_date_to_slash( $date ) {
		if ( 8 !== strlen( $date ) ) {
			return false;
		}

		$date = DateTime::createFromFormat( 'Ymd', $date );

		if ( $date ) {
			return $date->format( 'm/d/Y' );
		}

		return false;
	}

	/**
	 * Take a date string separated by "/" and turn it into a string of
	 * YYYYMMDD for better sorting in the database and on output.
	 *
	 * @param string $date
	 *
	 * @return bool|string
	 */
	public function slash_date_to_string( $date ) {
		if ( 10 !== strlen( $date ) ) {
			return false;
		}

		if ( 2 !== substr_count( $date, '/' ) ) {
			return false;
		}

		$date = DateTime::createFromFormat( 'm/d/Y', $date );

		if ( $date ) {
			return $date->format( 'Ymd' );
		}

		return false;
	}

	/**
	 * Save all of the meta data associated with a timeline point when saved.
	 *
	 * @param int     $post_id ID of the post currently being saved.
	 * @param WP_Post $post    Object of the post being saved.
	 */
	public function save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( $this->point_content_type_slug !== $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['_wsu_timeline_point_nonce'] ) || ! wp_verify_nonce( $_POST['_wsu_timeline_point_nonce'], 'wsu-timeline-save-point' ) ) {
			return;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		if ( isset( $_POST['wsu_tp_sub_headline'] ) && ! empty( trim( $_POST['wsu_tp_sub_headline'] ) ) ) {
			update_post_meta( $post_id, '_wsu_tp_sub_headline', sanitize_text_field( $_POST['wsu_tp_sub_headline'] ) );
		} else {
			delete_post_meta( $post_id, '_wsu_tp_headline' );
		}

		if ( isset( $_POST['wsu_tp_start_date'] ) && ! empty( trim( $_POST['wsu_tp_start_date'] ) ) ) {
			$start_date = $this->slash_date_to_string( $_POST['wsu_tp_start_date'] );

			if ( $start_date ) {
				update_post_meta( $post_id, '_wsu_tp_start_date', $start_date );
			}
		} else {
			delete_post_meta( $post_id, '_wsu_tp_start_date' );
		}

		if ( isset( $_POST['wsu_tp_end_date'] ) && ! empty( trim( $_POST['wsu_tp_end_date'] ) ) ) {
			$end_date = $this->slash_date_to_string( $_POST['wsu_tp_end_date'] );

			if ( $end_date ) {
				update_post_meta( $post_id, '_wsu_tp_end_date', $end_date );
			}
		} else {
			delete_post_meta( $post_id, '_wsu_tp_end_date' );
		}

		if ( isset( $_POST['wsu_tp_video_url'] ) && ! empty( trim( $_POST['wsu_tp_video_url'] ) ) ) {
			update_post_meta( $post_id, '_wsu_tp_video_url', esc_url_raw( $_POST['wsu_tp_video_url'] ) );
		} else {
			delete_post_meta( $post_id, '_wsu_tp_video_url' );
		}

		if ( isset( $_POST['wsu_tp_external_url'] ) && ! empty( trim( $_POST['wsu_tp_external_url'] ) ) ) {
			update_post_meta( $post_id, '_wsu_tp_external_url', esc_url_raw( $_POST['wsu_tp_external_url'] ) );
		} else {
			delete_post_meta( $post_id, '_wsu_tp_external_url' );
		}

		if ( isset( $_POST['wsu_tp_submitter_name'] ) && ! empty( trim( $_POST['wsu_tp_submitter_name'] ) ) ) {
			update_post_meta( $post_id, '_wsu_tp_submitter_name', sanitize_text_field( $_POST['wsu_tp_submitter_name'] ) );
		} else {
			delete_post_meta( $post_id, '_wsu_tp_submitter_name' );
		}

		if ( isset( $_POST['wsu_tp_submitter_email'] ) && ! empty( trim( $_POST['wsu_tp_submitter_email'] ) ) ) {
			update_post_meta( $post_id, '_wsu_tp_submitter_email', sanitize_text_field( $_POST['wsu_tp_submitter_email'] ) );
		} else {
			delete_post_meta( $post_id, '_wsu_tp_submitter_email' );
		}

		if ( isset( $_POST['wsu_tp_submitter_phone'] ) && ! empty( trim( $_POST['wsu_tp_submitter_phone'] ) ) ) {
			update_post_meta( $post_id, '_wsu_tp_submitter_phone', sanitize_text_field( $_POST['wsu_tp_submitter_phone'] ) );
		} else {
			delete_post_meta( $post_id, '_wsu_tp_submitter_phone' );
		}

		if ( isset( $_POST['wsu_tp_story_source'] ) && ! empty( trim( $_POST['wsu_tp_story_source'] ) ) ) {
			update_post_meta( $post_id, '_wsu_tp_story_source', wp_kses_post( $_POST['wsu_tp_story_source'] ) );
		} else {
			delete_post_meta( $post_id, '_wsu_tp_story_source' );
		}

		return;
	}

	/**
	 * Modify the columns displayed in the list table for timeline items.
	 *
	 * @param array $post_columns Existing list of columns to display.
	 *
	 * @return array Modified list of columns to display.
	 */
	public function manage_item_posts_columns( $post_columns ) {
		unset( $post_columns['cb'] );
		unset( $post_columns['title'] );
		$new_post_columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => 'Headline',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
		);
		$post_columns = array_merge( $new_post_columns, $post_columns );

		return $post_columns;
	}

	/**
	 * Output data associated with a custom column in the timeline item list table.
	 *
	 * @param string $column_name Column being displayed in the row.
	 * @param int    $post_id     ID of the current row being displayed.
	 */
	public function manage_item_posts_custom_column( $column_name, $post_id ) {
		if ( 'start_date' === $column_name ) {
			$start_date = get_post_meta( $post_id, '_wsu_tp_start_date', true );
			$date = $this->string_date_to_slash( $start_date );
			if ( $date ) {
				echo $date;
			} else {
				echo esc_html( $start_date );
			}
		}

		if ( 'end_date' === $column_name ) {
			$end_date = get_post_meta( $post_id, '_wsu_tp_end_date', true );
			$date = $this->string_date_to_slash( $end_date );
			if ( $date ) {
				echo $date;
			} else {
				echo esc_html( $end_date );
			}
		}
	}

	/**
	 * Change the post title placeholder text to better match the intent of this post type.
	 *
	 * @param string  $title_placeholder Current placeholder text.
	 * @param WP_Post $post              Current post object.
	 *
	 * @return string Replacement placeholder text.
	 */
	public function modify_enter_title_text( $title_placeholder, $post ) {
		if ( $this->point_content_type_slug === $post->post_type ) {
			return 'Enter item headline';
		}

		return $title_placeholder;
	}

	/**
	 * Retrieve a list timeline items.
	 *
	 * @return WP_Query
	 */
	public function get_timeline_items() {
		$args = array(
			'post_type' => $this->point_content_type_slug,
			'posts_per_page' => 2000,
			'order'     => 'ASC',
			'meta_key' => '_wsu_tp_start_date',
			'orderby'   => 'meta_value_num',
		);
		$query = new WP_Query( $args );
		wp_reset_query();
		return $query;
	}
}
$wsu_timeline = new WSU_Timeline();

function wsu_timeline_slash_date_to_string( $date ) {
	global $wsu_timeline;
	return $wsu_timeline->slash_date_to_string( $date );
}

/**
 * Wrapper to retrieve a list of timeline items.
 *
 * @return WP_Query
 */
function wsu_timeline_get_items() {
	global $wsu_timeline;
	return $wsu_timeline->get_timeline_items();
}