<?php 

add_action( 'wp_enqueue_scripts', 'brave_blank_enqueue_styles' );
function brave_blank_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ) );
}

function brave_blank_custom_post_unit() {
  $labels = array(
    'name'               => _x( 'Units', 'A collection of Units from Brave Frontier' ),
    'singular_name'      => _x( 'Unit', 'A single unit' ),
    'add_new'            => _x( 'Add New', 'unit' ),
    'add_new_item'       => __( 'Add New Unit' ),
    'edit_item'          => __( 'Edit Unit' ),
    'new_item'           => __( 'New Unit' ),
    'all_items'          => __( 'All Units' ),
    'view_item'          => __( 'View Unit' ),
    'search_items'       => __( 'Search Units' ),
    'not_found'          => __( 'Unit not found' ),
    'not_found_in_trash' => __( 'No units found in the Trash' ), 
    'parent_item_colon'  => '',
    'menu_name'          => 'Units'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Units and their associated data from Brave Frontier',
    'public'        => true,
    'menu_position' => 5,
    'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
	'menu_icon' =>  get_stylesheet_directory_uri() .'/images/38.jpg', // 32px32
    'has_archive'   => true,
  );
  register_post_type( 'unit', $args ); 
}
add_action( 'init', 'brave_blank_custom_post_unit' );