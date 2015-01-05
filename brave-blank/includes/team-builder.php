<?php
add_action("wp_ajax_unit_search", "ajax_unit_search");

function ajax_unit_search() {
	$results  = array();
	$results['type'] = "success";

	if ( !wp_verify_nonce( $_GET['nonce'], "unit_search")) {
		$results['message'] = "Ahh Ahh Ahh. You didn't say the magic word.";
		$results = json_encode($results);
		echo $results;
		die();
	} 

	$args = array(
		'post_type'			=> 'unit',
		's'					=> $_GET['q'],
		'posts_per_page' 	=> -1
	);

	$results  = array();
	$results['type'] = "success";
	$units = new WP_Query( $args ); 
   	if ( $units->have_posts() ) {
   		$results['units'] = array();
		while ( $units->have_posts() ) { $units->the_post(); 
			$unit = array(
				'unit_name'		=> get_the_title(),
				'icon'			=> get_the_post_thumbnail(),
				'rarity'		=> get_field('rarity'),
				'unit_number'	=> get_field('unit_number')
			);
			array_push($results['units'], $unit);
		}
	}
	
	$results = json_encode($results);
	echo $results;
	die();
}