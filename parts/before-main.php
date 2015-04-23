<?php

/*
 * On the home page, we display a series of navigation menus at the top.
 */
if ( is_front_page() ) :

	$mega_menu_args = array(
		'theme_location'  => 'mega-menu',
		'menu'            => 'mega-menu',
		'container'       => 'div',
		'container_class' => 'mega-menu-wrapper',
		'container_id'    => 'mega-menu',
		'menu_class'      => null,
		'menu_id'         => null,
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 5,
	);

	$header_mega_menu_args = array(
		'theme_location'  => 'mega-menu',
		'menu'            => 'mega-menu',
		'container'       => 'div',
		'container_class' => 'mega-menu-labels-wrapper',
		'container_id'    => 'mega-menu-labels',
		'menu_class'      => null,
		'menu_id'         => null,
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 1,
	);

	$signature_menu_args = array(
		'theme_location'  => 'signature-menu',
		'menu'            => 'signature-menu',
		'container'       => 'div',
		'container_class' => 'signature-menu-wrapper',
		'container_id'    => 'signature-menu',
		'menu_class'      => null,
		'menu_id'         => null,
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 1,
	);

	$wsu_search_args = array(
		'theme_location'  => 'quick-links',
		'menu'            => 'quick-links',
		'container'       => 'div',
		'container_class' => false,
		'container_id'    => 'quick-links',
		'menu_class'      => null,
		'menu_id'         => null,
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 2,
	);

	$wsu_campus_args = array(
		'theme_location'  => 'top-level-links',
		'menu'            => 'top-level-links',
		'container'       => 'div',
		'container_class' => false,
		'container_id'    => 'top-level-links',
		'menu_class'      => null,
		'menu_id'         => null,
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 1,
	);

	$timeline_site = wsuwp_get_current_site();

	if ( 'timeline.wsu.dev' === $timeline_site->domain ) {
		$home_domain = 'wp.wsu.dev';
		$home_path = '/wsu-home/';
	} else {
		$home_domain = 'wsu.edu';
		$home_path = '/';
	}
	$home_site = get_blog_details( array( 'domain' => $home_domain, 'path' => $home_path ) );

	if ( isset( $home_site->blog_id ) ) {
		switch_to_blog( $home_site->blog_id );
	}

	?>
	<header class="main-header wsu-home-navigation">
		<div class="header-shelf-wrapper">
			<section class="single triptych row header-shelf">
				<div class="column one">
					<div class="wsu-logo">
						<a href="https://wsu.edu/"><img src="https://wsu.edu/125/wp-content/themes/wsu-home/images/wsu-home-logo.svg"></a>
					</div>
				</div>
				<div class="column two wsu-mega-nav-labels">
					<?php echo wsu_timeline_get_menu( $header_mega_menu_args ); ?>
				</div>
				<div class="column three wsu-other-nav-placeholder">
					<div class="top-level-links-label">WSU Locations</div>
					<div class="search-label">Search</div>
				</div>
			</section>
		</div>
		<div class="header-drawer-wrapper">
			<section class="single triptych row header-drawer">
				<div class="column one wsu-signature-nav-container">
					<?php echo wsu_timeline_get_menu( $signature_menu_args ); ?>
				</div>
				<div class="column two wsu-mega-nav-container">
					<?php echo wsu_timeline_get_menu( $mega_menu_args ); ?>
				</div>
				<div class="column three">
					<!-- Empty with purpose. -->
				</div>
			</section>
			<div class="close-header-drawer">x</div>
		</div>
		<!-- Search interface, hidden by default until interaction in header -->
		<div class="header-search-wrapper header-search-wrapper-hide">
			<section class="side-right row" id="search-modal">
				<div class="column one">
					<div class="header-search-input-wrapper">
						<form method="get" action="https://search.wsu.edu/Default.aspx">
							<input name="cx" value="013644890599324097824:kbqgwamjoxq" type="hidden">
							<input name="cof" value="FORID:11" type="hidden">
							<input name="sa" value="Search" type="hidden">
							<input name="fp" value="true" type="hidden">
							<label for="header-search">Search</label>
							<input type="text" value="" name="q" placeholder="Search" class="header-search-input" />
						</form>
					</div>
					<div class="header-search-a-z-wrapper">
						<span class="search-a-z"><a href="http://index.wsu.edu/">A-Z Index</a></span>
					</div>
				</div>
				<div class="column two">
					<div class="quick-links-label">Common Searches</div>
					<?php echo wsu_timeline_get_menu( $wsu_search_args ); ?>
				</div>
			</section>
			<div class="close-header-search">x</div>
		</div>

		<!-- Campus links, hidden by default until interaction in header -->
		<div class="campus-links-full-page-wrapper campus-links-hide">
			<div class="campus-links-close">x</div>
			<div class="campus-links-internal-wrapper">
				<section class="single full row" id="campus-modal">
					<div class="column one">
						<?php echo wsu_timeline_get_menu( $wsu_campus_args ); ?>
					</div>
				</section>
			</div>
		</div>
	</header>
	<?php

	if ( ms_is_switched() ) {
		restore_current_blog();
	}

endif;