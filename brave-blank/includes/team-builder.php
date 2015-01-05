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

add_action("wp_ajax_add_unit", "ajax_add_unit");

function ajax_add_unit() {
	$results  = array();

	if ( !wp_verify_nonce( $_GET['nonce'], "add_unit")) {
		$results['message'] = "Ahh Ahh Ahh. You didn't say the magic word.";
		$results['type'] = "error";
		$results = json_encode($results);
		echo $results;
		die();
	} 

	$args = array(
		'post_type'			=> 'unit',
		'meta_key' 			=> 'unit_number',
		'meta_value' 		=> $_GET['unit-id'],
		'posts_per_page' 	=> -1
	);

	$unit_query = new WP_Query( $args ); 
   	if ( $unit_query->have_posts() ) {
		while ( $unit_query->have_posts() ) { $unit_query->the_post();
			$user_id = get_current_user_id();
			$units = get_user_option('brave_unit_roster', $user_id);
			//var_dump($units);
			if ($units == false){
				$units = array();
			}
			$unit = array(
				'unit_name'		=> get_the_title(),
				'icon'			=> get_the_post_thumbnail(),
				'rarity'		=> get_field('rarity'),
				'unit_number'	=> get_field('unit_number'),
				'unit_type'		=> $_GET['unit-type'],
				'hp'			=> get_field($_GET['unit-type'].'_hp'),
				'attack'		=> get_field($_GET['unit-type'].'_atk'),
				'defense'		=> get_field($_GET['unit-type'].'_def'),
				'recovery'		=> get_field($_GET['unit-type'].'_rec'),
			);
			array_push($units, $unit);
			update_user_option($user_id, 'brave_unit_roster', $units);
			$results['type'] = "success";
			$results['units'][0] = $unit;
		}
	}else{
		$results['type'] = "error";
		$results['message'] = "Could not find unit.";
	}
	
	$results = json_encode($results);
	echo $results;
	die();
}

add_action("wp_ajax_remove_unit", "ajax_remove_unit");

function ajax_remove_unit() {
	$results  = array();

	if ( !wp_verify_nonce( $_GET['nonce'], "remove_unit")) {
		$results['message'] = "Ahh Ahh Ahh. You didn't say the magic word.";
		$results['type'] = "error";
		$results = json_encode($results);
		echo $results;
		die();
	} 

	
	$user_id = get_current_user_id();
	$units = get_user_option('brave_unit_roster', $user_id);
	//var_dump($units);
	if(count($units) != 0){
		for($i = 0; $i < count($units); $i++){
			if ($units[$i]['unit_number'] == $_GET['unit-id']){
				unset($units[$i]);
			}
		}
		$revised = array_values($units);
		update_user_option($user_id, 'brave_unit_roster', $revised);
		$results['type'] = "success";
		$results['message'] = "Units(s) matching have been removed.";
	}else{
		$results['type'] = "error";
		$results['message'] = "Could not find unit.";
	}
	
	$results = json_encode($results);
	echo $results;
	die();
}