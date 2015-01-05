<?php
/**
 * Handle POST submission
 *
 * @param array $options
 * @return void
 */
function process_unit_import($options) {
	if (empty($_FILES['unit_import']['tmp_name'])) {
		$error = 'No file uploaded, aborting.';
		print_messages($error);
		return;
	}

	if (!current_user_can('import')) {
		$error = 'You don\'t have the permissions to import. Please contact the blog\'s administrator.';
		print_messages($error);
		return;
	}
	
	check_admin_referer( 'brave_blank_unit_import_verify' );

	require_once 'DataSource.php';

	$time_start = microtime(true);
	$csv = new File_CSV_DataSource;
	$file = $_FILES['unit_import']['tmp_name'];
	stripBOM($file);

	if (!$csv->load($file)) {
		$error = 'Failed to load file, aborting.';
		print_messages($error);
		return;
	}

	// pad shorter rows with empty values
	// $csv->symmetrize();

	$skipped = 0;
	$imported = 0;
	foreach ($csv->connect() as $csv_data) {
		$id = create_unit($csv_data);
		if ( is_numeric($id) ) {
			$imported++;
		} else {
			print_messages($id);
			return;
		}
	}

	if (file_exists($file)) {
		@unlink($file);
	}

	$exec_time = microtime(true) - $time_start;
	$notice = "";
	if ($skipped) {
		$notice += "<b>Skipped {$skipped} posts (most likely due to empty title, body and excerpt).</b></br>";
	}
	$notice += sprintf("<b>Imported {$imported} posts %.2f seconds.</b>", $exec_time);
	print_messages($notice);
}

function create_unit($data) {
	$fields = array(
        'unit_number',
		'element',
		'rarity',
		//'max_level',
		'cost',
		'hit_count','bb_hits','sbb_hits',
		'bb_fill','sbb_fill',
		'lord_hp','lord_atk','lord_def','lord_rec',
		'anima_hp','anima_atk','anima_def','anima_rec',
		'breaker_hp','breaker_atk','breaker_def','breaker_rec',
		'guardian_hp','guardian_atk','guardian_def','guardian_rec',
		'oracle_hp','oracle_atk','oracle_def','oracle_rec',
		//'max_hp_bonus', 'max_atk_bonus', 'max_def_bonus', 'max_rec_bonus', 
		'leaders_skill_effect', 'brave_burst_effect', 'super_brave_burst_effect'
    );
	
	$type = 'unit';
	$valid_type = (function_exists('post_type_exists') && post_type_exists($type));

	if (!$valid_type) {
		//$this->log['error']["type-{$type}"] = sprintf('Unknown post type "%s".', $type);
	}

	$new_post = array(
		'post_title'   => convert_chars($data['unit_name']),
		'post_content' => 'Not available',
		'post_status'  => 'publish',
		'post_type'    => $type,
		'post_date'    => parse_date(time())
	);

	// create!
	$id = wp_insert_post($new_post);
	
	//Now that we have a new post ID update the associated field data
	foreach($fields as $field){
		if (array_key_exists($field,$data)){
			update_field($field, $data[$field], $id);
		}else{
			return "<b>The field {$field} does not exist.</b>";
		}
	}
	
	return $id;
}

/**
 * Convert date in CSV file to 1999-12-31 23:52:00 format
 *
 * @param string $data
 * @return string
 */
function parse_date($data) {
	$timestamp = strtotime($data);
	if (false === $timestamp) {
		return '';
	} else {
		return date('Y-m-d H:i:s', $timestamp);
	}
}

/**
 * Delete BOM from UTF-8 file.
 *
 * @param string $fname
 * @return void
 */
function stripBOM($fname) {
	$res = fopen($fname, 'rb');
	if (false !== $res) {
		$bytes = fread($res, 3);
		if ($bytes == pack('CCC', 0xef, 0xbb, 0xbf)) {
			$this->log['notice'][] = 'Getting rid of byte order mark...';
			fclose($res);

			$contents = file_get_contents($fname);
			if (false === $contents) {
				trigger_error('Failed to get file contents.', E_USER_WARNING);
			}
			$contents = substr($contents, 3);
			$success = file_put_contents($fname, $contents);
			if (false === $success) {
				trigger_error('Failed to put file contents.', E_USER_WARNING);
			}
		} else {
			fclose($res);
		}
	} else {
		//$this->log['error'][] = 'Failed to open file, aborting.';
	}
}

function print_messages($message = ""){
	wp_redirect(  admin_url( 'edit.php?post_type=unit&page=home2/moto/public_html/wpms/wp-content/themes/brave-blank/includes/unit-import/menus.inc.php_upload_form&message=' . $message ) );
   exit;
}