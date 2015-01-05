<?php
/**
 * Handle POST submission for BFP Connector Update
 *
 * @param array $options
 * @return void
 */
function bfp_connector_model($options) {
	if (!current_user_can('import')) {
		$error = 'You don\'t have the permissions to import. Please contact the blog\'s administrator.';
		return;
	}
	
	check_admin_referer( 'bfp_connector' );

	$table_id = "1C7oKd1ko3PvX2mcOkw6es4aYP2D5o_aMFgFwMN5Z";

	//field array for table
	$table_fields = array(
		"unit_number", "title",	"series_not_used", "element", "rarity", 
		"leaders_skill_effect", "brave_burst_effect", "super_brave_burst_effect",
		"bb_type_not_used", "sbb_type_not_used",
		"cost", "max_level", "hit_count", "bb_hits", "sbb_hits",
		"bb_fill", "sbb_fill", "evo_zel_not_used",
		"acquire_not_used",	"gender_not_used", "bfpro_notes_not_used",
		"batch_not_used", "type_pref_not_used",
		"global_release_not_used", "japan_release_not_used",
		"weighted_stats_not_used",
		"lord_hp", "lord_atk", "lord_def", "lord_rec",
		"anima_hp", "anima_atk", "anima_def", "anima_rec",
		"breaker_hp", "breaker_atk", "breaker_def", "breaker_rec",
		"guardian_hp", "guardian_atk", "guardian_def", "guardian_rec",
		"oracle_hp", "oracle_atk", "oracle_def", "oracle_rec",
		"hp_range_not_used", "stats_rnage-not_used",
		"max_hp_bonus", "max_atk_bonus", "max_def_bonus", "max_rec_bonus",
		"weighted_pimp_stats_not_used", 
		"evo1_not_used", "evo2_not_used", "evo3_not_used", "evo4_not_used", "evo5_not_used",
		"icon", "image", "video"
	);

	$time_start = microtime(true);
	//Get the google services auto loader started
	include('autoload.php');

	//OAuth Impersination
	$client_email = '170115078503-187qff85efbgnfcbvhgraf8kklthfaq8@developer.gserviceaccount.com';
	$private_key = file_get_contents(__DIR__ . '/BFP-Table-Consumer-ccbd07bce40f.p12');
	$scopes = array('https://www.googleapis.com/auth/fusiontables.readonly');
	$credentials = new Google_Auth_AssertionCredentials(
	    $client_email,
	    $scopes,
	    $private_key
	);
	
	$client = new Google_Client();
	$client->setAssertionCredentials($credentials);
	if ($client->getAuth()->isAccessTokenExpired()) {
	  $client->getAuth()->refreshTokenWithAssertion();
	}

	$service = new Google_Service_Fusiontables($client);
	$results = $service->query->sqlGet("SELECT * FROM " . $table_id . " WHERE 'Global Release'='true' AND ID=".$_POST['unit_number']); // ID=".$_POST['unit_number'])
	//var_dump($results);
	$log_messages = array();
	if( count($results['rows'])) != 0){
		foreach ($results['rows'] as $row) {
			$unit_data = array(
				'description'	=> "N/A",
				'brave_burst'	=> "N/A",
				'super_brave_burst'	=> "N/A",
				'leader_skill'	=> "N/A",
			);
			$count = 0;
			foreach ($row as $column_value){
				$unit_data[$table_fields[$count]] = $column_value;
				$count++;
			}
			$unit = new unitModel;
			$unit_data = $unit->create_update_unit($unit_data);
			if(isset($unit_data['success'])){
				array_push($log_messages, $unit_data['success']);
			}elseif(isset($unit_data['errors'])){
				if(is_array($unit_data['errors'])){
					foreach($unit_data['errors'] as $error){
						array_push($log_messages, $error);
					}
				}else{
					array_push($log_messages, $unit_data['errors']);
				}
			}else{
				array_push($log_messages, "Unit not updated or created.");
			}
		}
	}

	$exec_time = microtime(true) - $time_start;
	echo "<b>Request processed in $exec_time seconds.</b><br/>";
	foreach ($log_messages as $message){
		echo $message . "<br/>";
	}
}
