<?php
/*
* Plugin Name: Unit CSV Import
* Description: A simple wordpress plugin to import units into the Brave Blank custom post type
* Author: Joe Motacek
* Version: 0.0.3
* Author URI: http://www.joemotacek.com
*/

// Include or Require any files
include('unit-import/unit_import_model.php');
include('unit-import/unit_import_view.php');
include('unit-import/menus.inc.php');
 
// Action & Filter Hooks
add_action('admin_menu', 'unit_import_admin_menu');
add_action( 'admin_post_brave_blank_unit_import', 'process_unit_import' );
?>
