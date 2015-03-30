<?php

get_header();
?>
<main>
	<section class="single row">
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
		<header>
			<h2><?php the_title(); ?></h2>
			<?php if ( ! empty( $timeline_sub_headline ) ) : ?><h3><?php echo $timeline_sub_headline; ?></h3><?php endif; ?>
		</header>
		<?php
		if ( $item_has_featured_image ) {
			echo '<figure class="timeline-featured-image"><img src="' . esc_url( spine_get_featured_image_src( 'spine-small_size' ) ) . '"></figure>';
		}
		?>
		<span class="start-date"><?php echo esc_html( $start_date ); ?></span>
		<span class="end-date"><?php echo esc_html( $end_date ); ?></span>
		<span class="external-url"><a href="<?php echo esc_url( $external_url ); ?>"><?php echo esc_url( $external_url ); ?></a></span>
		<div class="timeline-content timeline-content-<?php echo $column_class; ?>">
			<?php the_content(); ?>
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