<?php 

/*The idea here is that we pull in all the units and calculate their usefulness based on a variety of factors.  Then after we have 
    their individual stats we apply "weights" to them individually and by section so that we can tweak their placement in the table 
    BEFORE the voting starts.  This way once peopel start voting it's jsut another weighted stat in the calculation which can 
    drive the unit's overall placement or RANK.  Once this data is calculated and stored in the Unit's cusotm post type we display it.  
    
    This page should only focuses on displaying the data NOT doing the necessary calculations to determine the rankings.  The calculations 
    should be done by a plugin created in the back end that can be manually run or cron jobbed... much of the calculation code currently 
    located here will be moved to the back end after it's debugged.
    
    Version 0.0.2 Update:
    so right now the queries actually run quite quickly but I only have 5 sample units in the BE so....
    At this point the basics are complete the weights and such but I need to talk to Kuhaku about the finer point of how units 
    should be ranked.  For exmaple I'mnot calculating:
    - ldr_skill_value
    - synergy_val
    - bb_value 
    at this point I'm not sure how these value shoudl be assigned/evaluated.
    
    Verison 0.0.3 
    added Stars and flags... not sure if they work yet...
    
    Version 0.0.4
    add voting...
    Version 0.0.5
    Added Normalization 
    Version 0.0.6 
    Adjusted forumla
    Added more debuggin info for dev version.
    
    Version 0.0.7
    -Added simple inversion to the calculation ot the normalizaiton function so the fill rate would be higher scored when lower.
    
    Version 0.0.8
    -Added tiers

    Version 0.0.9
    - Added submit unit form

    Version 0.1.0
    Moved unit update calculations to a scheduled task in function.php

    Version 0.1.1 
    -Bug fixes:
        - Post content update on Unit Submit page
        - Use field_key instead of field_name for update_field()

    Version 0.1.2
    - Ditched CSV importer for Brave Frontier Pros data connector

    Version 0.1.3
    - Moved unit creation and update funciton to an include
    - Created a model for units

    Version 0.1.4 
    - Got Fusion Tables working...
    - Added support for unit icons

    Version 0.1.5
    - Bug fixes for post icons
    - Added columns to unit manager

    Version 0.1.6
    - Moved Unit post type to include
    - Moved Cron to include

    Version 0.1.7
    - Added Unit roster creation tool

*/

//Child Theme Includes
add_action( 'wp_enqueue_scripts', 'brave_blank_enqueue_styles' );
function brave_blank_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ) );
}

//Unit Post Type function
include( plugin_dir_path( __FILE__ ) . 'includes/unit-post-type.php');

//Unit Import Plugin
//include( plugin_dir_path( __FILE__ ) . 'includes/unit-importer.php');

//Unit Creation Model function
include( plugin_dir_path( __FILE__ ) . 'includes/unit-model.php');

//Unit Brave Frontier PROs Import Plugin
include( plugin_dir_path( __FILE__ ) . 'includes/bfp-connector.php');

//Update Ranking Cron
include( plugin_dir_path( __FILE__ ) . 'includes/update-rankings.php');

//Team Builder
include( plugin_dir_path( __FILE__ ) . 'includes/team-builder.php');

//Replace post meta for single units.
if ( ! function_exists( 'brave_blank_post_meta' ) ) {
    function brave_blank_post_meta() {
        
        $meta = get_post_custom( get_the_ID() );
        
        $date_label  = __( 'Last update on', 'braveblank' );
        $date_value = get_post_modified_time( get_option( 'date_format' ), '', '', true );
        
        if ( ! is_singular() ) {
            $date_label = __( 'Posted', 'braveblank' );
            $date_value = human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'braveblank' );
        }
?>
<aside class="post-meta">
    <ul>
        <?php
            if ( isset( $meta['_source_name'] ) && ( $meta['_source_name'][0] != '' ) ) {
        ?>
        <li class="post-source">
            <span class="small"><?php _e( 'From', 'braveblank' ); ?></span>
            <?php echo ' ' . $meta['_source_name'][0] . ' &bull; '; ?>
        </li>
        <?php
            }
        ?>
        <li class="post-date">
            <span class="small"><?php echo $date_label; ?></span>
            <?php echo $date_value; ?>
        </li>
        <li class="post-category">
            <span class="small"><?php _e( 'under', 'braveblank' ); ?></span>
            <?php the_category( ', ' ); ?>
        </li>
        <li class="edit"><a href="<?php echo site_url('submit-unit/?post_id='. get_the_ID()); ?>">Edit Unit</a></li>
    </ul>
</aside>
<?php
    } // End woo_post_meta()
}