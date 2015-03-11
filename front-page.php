<?php

get_header();
?>

<section class="row single gutter pad-ends">
	<div class="column one">Our Timeline.</div>
</section>

<?php

$timeline_query = wsu_timeline_get_items();

$flip_flop = 0;
while( $timeline_query->have_posts() ) {
	$timeline_query->the_post();

	if ( 0 === $flip_flop ) {
		$column_class = 'one';
		echo '<section class="row halves">';
	} else {
		$column_class = 'two';
	}

	$timeline_sub_headline = get_post_meta( get_the_ID(), '_wsu_tp_sub_headline', true );

	?>
	<div class="column <?php echo $column_class; ?>">
		<div class="timeline-item-container">
			<header>
				<h2><?php the_title(); ?></h2>
				<?php if ( ! empty( $timeline_sub_headline ) ) : ?><h3><?php echo $timeline_sub_headline; ?></h3><?php endif; ?>
			</header>
			<?php
			if ( spine_has_featured_image() ) {
				echo '<figure><img src="' . esc_url( spine_get_featured_image_src( 'spine-small_size' ) ) . '"></figure>';
			}
			?>
			<div class="timeline-content timeline-content-<?php echo $column_class; ?>">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
	<?php

	if ( 0 === $flip_flop ) {
		$flip_flop++;
	} else {
		$flip_flop = 0;
		echo '</section>';
	}
}


get_footer();