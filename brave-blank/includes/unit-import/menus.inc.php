<?php
//Add menu option to post type
function unit_import_admin_menu() {
    add_submenu_page(
		'edit.php?post_type=unit', 
		'Unit Importer', 
		'Unit Import', 
		'import', 
		__FILE__ . '_upload_form', 
		'upload_form'
	);
}