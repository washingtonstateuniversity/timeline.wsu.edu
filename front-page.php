<?php

get_header();

// Set a starting century and decade for the timeline.
$timeline_century = 1800;
$item_century     = 1800;
$timeline_decade  = 1890;
$item_decade      = 1890;
$item_year        = 1890;
$item_month       = 189001;

// Used to control the `one` and `two` column classes when alternating items.
$flip_flop = 0;

$timeline_decades = wsu_timeline_get_decades();

?>
<main>
	<section class="row single gutter pad-top main-feature-top full">
		<div class="column one ">
			<figure class="wsu-p-replaced">
				<img class="alignnone" src="https://wsu.edu/wp-content/uploads/sites/625/2015/03/cougar-head-white.svg" alt="Washington State University Cougar Head" width="300" height="150">
			</figure>
			<h1 class="hero">Inspiring<br>
				Washington</h1>
			<h2>125 years, and counting.</h2>
		</div>
	</section>
	<?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>
	<section class="row single">
		<div class="column one">
			<?php the_content(); ?>
		</div>
	</section>
	<?php endwhile; endif; ?>
	<section class="row single scrub">
		<div class="scrub-shade-overlay"></div>
		<div class="scrub-progress-bar"></div>
		<div class="column one">
			<div class="scrub-mark" data-decade="1890"><span class="scrub-mark-text">– 1890</span></div>
			<div class="scrub-mark" data-decade="1900"><span class="scrub-mark-text">– 1900</span></div>
			<div class="scrub-mark" data-decade="1910"><span class="scrub-mark-text">– 1910</span></div>
			<div class="scrub-mark" data-decade="1920"><span class="scrub-mark-text">– 1920</span></div>
			<div class="scrub-mark" data-decade="1930"><span class="scrub-mark-text">– 1930</span></div>
			<div class="scrub-mark" data-decade="1940"><span class="scrub-mark-text">– 1940</span></div>
			<div class="scrub-mark" data-decade="1950"><span class="scrub-mark-text">– 1950</span></div>
			<div class="scrub-mark" data-decade="1960"><span class="scrub-mark-text">– 1960</span></div>
			<div class="scrub-mark" data-decade="1970"><span class="scrub-mark-text">– 1970</span></div>
			<div class="scrub-mark" data-decade="1980"><span class="scrub-mark-text">– 1980</span></div>
			<div class="scrub-mark" data-decade="1990"><span class="scrub-mark-text">– 1990</span></div>
			<div class="scrub-mark" data-decade="2000"><span class="scrub-mark-text">– 2000</span></div>
			<div class="scrub-mark" data-decade="2010"><span class="scrub-mark-text">– 2010</span></div>
		</div>
	</section>
	<section class="halves row timeline-container">
		<div class="century century-1800">
			<div class="decade decade-1890">
<?php

if ( isset( $timeline_decades[ $timeline_decade ] ) ) {
	?>
	<div class="decade-item" style="background-image: url(<?php echo esc_url( $timeline_decades[ $timeline_decade ]['image'] ); ?>);">
		<span class="decade-item-title"><?php echo esc_html( $timeline_decades[ $timeline_decade ]['title'] ); ?></span>
		<div class="decade-item-content">
			<?php echo $timeline_decades[ $timeline_decade ]['content']; ?>
		</div>
	</div>
<?php
}

$timeline_query = wsu_timeline_get_items();

while( $timeline_query->have_posts() ) {
	$timeline_query->the_post();

	$column_class = ( 0 === $flip_flop ) ? 'one' : 'two';

	$timeline_sub_headline = get_post_meta( get_the_ID(), '_wsu_tp_sub_headline', true );
	$start_date            = get_post_meta( get_the_ID(), '_wsu_tp_start_date',   true );
	$end_date              = get_post_meta( get_the_ID(), '_wsu_tp_end_date',     true );
	$external_url          = get_post_meta( get_the_ID(), '_wsu_tp_external_url', true );

	if ( $start_date && 8 === strlen( $start_date ) ) {
		// Build the item's century using the first two year digits.
		$item_century = absint( substr( $start_date, 0, 2 ) . '00' );
		// Build the item's decade using the first three year digits.
		$item_decade  = absint( substr( $start_date, 0, 3 ) . '0' );
		// Build the item's year using the first four date digits.
		$item_year    = absint( substr( $start_date, 0, 4 ) );
		// Build the item's month using the first six date digits.
		$item_month   = absint( substr( $start_date, 0, 6 ) );

		$start_date = DateTime::createFromFormat( 'Ymd', $start_date );
		if ( $start_date ) {
			$start_date = $start_date->format( 'j F Y' );
		} else {
			$start_date = 'Invalid Start Date';
		}
	} else {
		$start_date = 'Invalid Start Date';
	}

	if ( $end_date && 8 === strlen( $end_date ) ) {
		$end_date = DateTime::createFromFormat( 'Ymd', $end_date );
		if ( $end_date ) {
			$end_date = $end_date->format( 'j F Y' );
		} else {
			$end_date = '';
		}
	} else {
		$end_date = ''; // We aren't too worried about an end date being available or valid.
	}

	// If a decade is ending, close out the container. This should be closed out
	// before any century container.
	if ( $item_decade >  $timeline_decade ) {
		echo '</div><!-- end decade ' . $timeline_decade . ' -->' . "\n";
	}

	// If a century is ending, close out the container. This should be closed out
	// after any decade container.
	if ( $item_century > $timeline_century ) {
		echo '</div><!-- end century ' . $timeline_century . '-->' ."\n";
	}

	// If a new century is beginning, start a container.
	if ( $item_century > $timeline_century ) {
		$timeline_century = $item_century;
		echo '<div class="century century-' . $timeline_century . '">';
	}

	// If a new decade is beginning, start a container.
	if ( $item_decade >  $timeline_decade ) {
		$timeline_decade = $item_decade;
		echo '<div class="decade decade-' . $timeline_decade . '">';

		if ( isset( $timeline_decades[ $timeline_decade ] ) ) {
			?>
			<div class="decade-item" style="background-image: url(<?php echo esc_url( $timeline_decades[ $timeline_decade ]['image'] ); ?>);">
				<span class="decade-item-title"><?php echo esc_html( $timeline_decades[ $timeline_decade ]['title'] ); ?></span>
				<div class="decade-item-content">
					<?php echo $timeline_decades[ $timeline_decade ]['content']; ?>
				</div>
			</div>
			<?php
		}
	}
	$column_classes = array(
		'column',
		$column_class,
		'timeline-item-container',
		'item-year-' . $item_year,
		'item-month-' . $item_month,
	);

	$item_has_featured_image = spine_has_featured_image();

	if ( $item_has_featured_image ) {
		$column_classes[] = 'item-has-featured-image';
	}
	?>
	<div class="<?php echo implode( ' ', $column_classes ); ?>">
		<?php if ( $item_has_featured_image ) : ?>
		<div class="timeline-item-feature-wrapper">
			<figure class="timeline-item-feature">
				<img src="<?php echo esc_url( spine_get_featured_image_src( 'spine-xlarge_size' ) ); ?>">
			</figure>
		</div>
		<?php endif; ?>
		<div class="timeline-item-internal-wrapper">
			<header>
				<h2><?php the_title(); ?></h2>
				<?php if ( ! empty( $timeline_sub_headline ) ) : ?><h3><?php echo $timeline_sub_headline; ?></h3><?php endif; ?>
			</header>
			<div class="timeline-item-date-wrapper">
				<span class="start-date"><?php echo esc_html( $start_date ); ?></span>
				<?php if ( ! empty( $end_date ) ) : ?><span class="end-date"><?php echo esc_html( $end_date ); ?></span><?php endif; ?>
			</div>
			<?php if ( ! empty( $external_url ) ) : ?><span class="external-url"><a href="<?php echo esc_url( $external_url ); ?>"><?php echo esc_url( $external_url ); ?></a></span><?php endif; ?>
			<div class="timeline-content timeline-content-<?php echo $column_class; ?>">
				<?php
				// Retrieve the item's content and prep for discovery of the first paragraph.
				$content = apply_filters( 'the_content', get_the_content() );
				$content = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $content );
				$content = str_replace( '<p>&nbsp;</p>', '', $content );
				$content = str_replace( '<p></p>', '', $content );
				$content = trim( $content );

				// Find a match for the first paragraph.
				preg_match( '/<p>(.*)<\/p>/', $content, $matches );

				// If a match exists, strip it from the remaining content. Remaining content
				// will then be used for the expanded view.
				if ( $matches && isset( $matches[0] ) ) {
					echo $matches[0];
					$content = str_replace( $matches[0], '', $content );
				}

				$show_more = true;
				$content = trim( $content );
				if ( empty( $content ) && false === $item_has_featured_image ) {
					$show_more = false;
				}

				if ( $show_more ) {
					?>
					<div class="timeline-content-expanded">
						<?php echo $content; ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="timeline-item-footer">
				<?php
				if ( $item_has_featured_image ) {
					echo '<figure class="timeline-featured-image"><img src="' . esc_url( spine_get_featured_image_src( 'spine-small_size' ) ) . '"></figure>';
				} else {
					echo '<div class="timeline-featured-empty"></div>';
				}
				?>
				<div class="timeline-item-footer-meta">
					<?php if ( $show_more ) : ?><span class="timeline-item-read-more">More</span><?php endif; ?>
					<div class="timeline-item-social">
						<a href="https://www.facebook.com/sharer/sharer.php?u=" class="facebook tracked">Facebook</a>
						<a href="https://twitter.com/intent/tweet?text=" target="_blank" class="twitter tracked">Twitter</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php

	$flip_flop = ( 0 === $flip_flop ) ? 1 : 0;

} // end while timeline_query->have_posts()
?>
			</div><!-- end decade -->
		</div><!-- end century -->
	</section>
</main>
<?php
get_footer();