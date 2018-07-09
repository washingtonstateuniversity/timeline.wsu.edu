<?php

/*
 * On the home page, we display a series of navigation menus at the top.
 */
if ( is_front_page() ) :

	$mega_menu_args = array(
		'theme_location'  => 'mega-menu',
		'menu'            => 'mega-menu',
		'container'       => '',
		'container_class' => '',
		'container_id'    => '',
		'menu_class'      => null,
		'menu_id'         => null,
		'items_wrap'      => '<ul class="nav-dropdown">%3$s</ul>',
		'depth'           => 5,
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

	$timeline_site = get_site();

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
	<header class="site-header-mega row">
		<div class="wsu-signature">
			<img src="https://wsu.edu/wp-content/themes/wsu-home/images/wsu-home-logo.svg" alt="Washington State University">
		</div>
		<nav class="main-navigation" id="wsu-home-primary-nav">
			<?php echo wsu_timeline_get_menu( $mega_menu_args ); ?>

			<div class="nav-close">
				<button>Close navigation</button>
			</div>

			<ul class="nav-search-give">
				<li class="nav-give">
					<a href="https://foundation.wsu">Give to WSU</a>
				</li>
				<li class="nav-search">
					<button>Search</button>
				</li>
			</ul>
		</nav>
	</header>

	<!-- Search interface, hidden by default until interaction in header -->
	<div class="header-search-wrapper">
		<section class="side-right row" id="search-modal">
			<div class="column one">
				<div class="header-search-input-wrapper">
					<form method="get" action="https://search.wsu.edu/Default.aspx">
						<input name="cx" value="002970099942160159670:yqxxz06m1b0" type="hidden">
						<input name="cof" value="FORID:11" type="hidden">
						<input name="sa" value="Search" type="hidden">
						<label for="header-search">Search</label>
						<input type="text" value="" name="q" placeholder="Search" class="header-search-input">
					</form>
				</div>
				<div class="header-search-a-z-wrapper">
					<span class="search-a-z">
						<a href="http://index.wsu.edu/">A-Z Index</a>
					</span>
				</div>
			</div>
			<div class="column two">
				<div class="quick-links-label">Common Searches</div>
				<?php echo wsu_timeline_get_menu( $wsu_search_args ); ?>
			</div>
		</section>
		<div class="close-header-search">
			<button>Close search</button>
		</div>
	</div>
	<!-- End search drawer block -->
	<?php

	if ( ms_is_switched() ) {
		restore_current_blog();
	}

endif;
