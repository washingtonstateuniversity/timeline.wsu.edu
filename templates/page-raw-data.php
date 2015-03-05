<?php /* Template Name: Timeline Output */

get_header();

$args = array(
	'post_type' => 'wsu-timeline-point',
	'posts_per_page' => 2000,
	'meta_key' => '_wsu_tp_start_date',
	'orderby', 'meta_value',
	'order', 'ASC',
);

$timeline_query = new WP_Query( $args );

?>
<main>
	<section class="row single gutter pad-ends">
		<table>
			<tr><th>ID</th><th>Title</th><th>Sub headline</th><th>Start Date</th><th>End Date</th></tr>
<?php

$timeline_items = array();
while ( $timeline_query->have_posts() ) {
	$timeline_query->the_post();
	$sub_headline = get_post_meta( get_the_ID(), '_wsu_tp_sub_headline', true );
	$start_date = get_post_meta( get_the_ID(), '_wsu_tp_start_date', true );
	$end_date = get_post_meta( get_the_ID(), '_wsu_tp_end_date', true );
	$external_url = get_post_meta( get_the_ID(), '_wsu_tp_external_url', true );

	// do ugly things to make up for bad data decisions.
	$start_time = strtotime( $start_date );
	while ( isset( $timeline_items[ $start_time ] ) ) {
		$start_time++;
	}

	$timeline_items[ $start_time ] = array(
		'id' => get_the_ID(),
		'url' => get_the_permalink(),
		'title' => get_the_title(),
		'sub-headline' => $sub_headline,
		'start_date' => $start_date,
		'end_date' => $end_date,
	);

}
wp_reset_query();
krsort( $timeline_items );

foreach ( $timeline_items as $item ) {
	echo '<tr><td>' . $item['id'] . '</td><td><a href="' . esc_url( $item['url'] ) . '">' . $item['title'] . '</a></td><td>' . $item['sub-headline'] . '</td><td>' . $item['start_date'] . '</td><td>' . $item['end_date'] . "</td></tr>\n";
}

?>
		</table>
	</section>
</main>
<?php
get_footer();