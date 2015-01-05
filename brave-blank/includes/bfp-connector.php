<?php
/*
* Plugin Name: Brave Frontier Pros Connector
* Description: Plugin that connects to BFPs fusion talbes to obtain data.
* Author: Joe Motacek
* Version: 0.0.3
* Author URI: http://www.joemotacek.com
*/
// Include or Require any files
include('bfp-connector/bfp_connector_model.php');
include('bfp-connector/bfp_connector_view.php');
include('bfp-connector/menus.inc.php');
 
// Action & Filter Hooks
add_action('admin_menu', 'bfp_connector_admin_menu');
add_action( 'admin_post_bfp_connector', 'bfp_connector_model' );
?>
