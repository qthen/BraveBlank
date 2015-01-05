<?php
//Add menu option to post type
function bfp_connector_admin_menu() {
    add_submenu_page(
		'edit.php?post_type=unit', 
		'BFP Connector', 
		'BFP Connector', 
		'import', 
		__FILE__ . '_bfp_connector_view', 
		'bfp_connector_form'
	);
}