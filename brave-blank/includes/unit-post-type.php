<?php
//Custom Post Type UNIT
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

function set_unit_columns($columns){
    return array(
        'cb'            => '<input type="checkbox" />',
        'featured'      => 'Icon',
        'unit_number'   => 'Number',
        'title'         => 'Title',
        'date'          => 'Date',
    );
}

function custom_unit_column($column, $post_id){
    switch ($column){
        case 'featured':
            echo get_the_post_thumbnail($post_id, array(50, 50) );
            break;
        case 'unit_number':
            echo get_field('unit_number', $post_id);
            break;
    }
}

function unit_register_sortable( $columns ){
    $columns['unit_number'] = 'unit_number';
    return $columns;
}

function unit_number_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'unit_number' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'unit_number',
            'orderby' => 'meta_value_num'
        ) );
    }
 
    return $vars;
}

add_action( 'init', 'brave_blank_custom_post_unit' );
add_filter("manage_edit-unit_columns", "set_unit_columns");
add_action("manage_unit_posts_custom_column", "custom_unit_column", 10 , 2);
add_filter("manage_edit-unit_sortable_columns", "unit_register_sortable" );
add_filter( 'request', 'unit_number_column_orderby' );