<?php

get_header();

// Set a starting century and decade for the timeline.
$timeline_century = 1800;
$item_century     = 1800;
$timeline_decade  = 1890;
$item_decade      = 1890;

// Used to control the `one` and `two` column classes when alternating items.
$flip_flop = 0;
?>
<main>
	<?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>
	<section class="single row">
		<div class="column one">
			<?php the_content(); ?>
		</div>
	</section>
	<?php endwhile; endif; ?>
	<section class="halves row">
		<div class="century-1800">
			<div class="decade-1890">
<?php

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
		echo '<div class="century-' . $timeline_century . '">';
	}

	// If a new decade is beginning, start a container.
	if ( $item_decade >  $timeline_decade ) {
		$timeline_decade = $item_decade;
		echo '<div class="decade-' . $timeline_decade . '">';
	}

	$item_has_featured_image = spine_has_featured_image();
	?>
	<div class="column <?php echo $column_class; ?> timeline-item-container <?php if ( $item_has_featured_image ) : echo 'item-has-featured-image'; endif; ?>">
		<div class="timeline-item-internal-wrapper">
			<header>
				<h2><?php the_title(); ?></h2>
				<?php if ( ! empty( $timeline_sub_headline ) ) : ?><h3><?php echo $timeline_sub_headline; ?></h3><?php endif; ?>
			</header>
			<?php
			if ( $item_has_featured_image ) {
				echo '<figure class="timeline-featured-image"><img src="' . esc_url( spine_get_featured_image_src( 'spine-small_size' ) ) . '"></figure>';
			}
			?>
			<div class="timeline-item-date-wrapper">
				<span class="start-date"><?php echo esc_html( $start_date ); ?></span>
				<?php if ( ! empty( $end_date ) ) : ?><span class="end-date"><?php echo esc_html( $end_date ); ?></span><?php endif; ?>
			</div>
			<?php if ( ! empty( $external_url ) ) : ?><span class="external-url"><a href="<?php echo esc_url( $external_url ); ?>"><?php echo esc_url( $external_url ); ?></a></span><?php endif; ?>
			<div class="timeline-content timeline-content-<?php echo $column_class; ?>">
				<?php
				$content = apply_filters( 'the_content', get_the_content() );
				$content = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $content );
				$content = strip_tags( $content, '<p><a><span><div><strong><em><b><i><sup><sub><ul><li><h1><h2><h3><h4><h5><h6>' );
				$content = str_replace( '<p>&nbsp;</p>', '', $content );
				$content = str_replace( '<p></p>', '', $content );
				echo $content;
				?>
				<a class="temporary-link" href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>">view</a>
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