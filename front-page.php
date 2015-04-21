<?php

get_header();
?>
<main>
	<section class="halves row">
<?php

$timeline_query = wsu_timeline_get_items();

$flip_flop = 0;
while( $timeline_query->have_posts() ) {
	$timeline_query->the_post();

	if ( 0 === $flip_flop ) {
		$column_class = 'one';
	} else {
		$column_class = 'two';
	}

	$timeline_sub_headline = get_post_meta( get_the_ID(), '_wsu_tp_sub_headline', true );
	$start_date = get_post_meta( $post->ID, '_wsu_tp_start_date', true );
	$end_date = get_post_meta( $post->ID, '_wsu_tp_end_date', true );
	$external_url = get_post_meta( $post->ID, '_wsu_tp_external_url', true );

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

	if ( 0 === $flip_flop ) {
		$flip_flop++;
	} else {
		$flip_flop = 0;
	}
}

?>
	</section>
</main>
<?php
get_footer();