<?php
//scheduler for unit updates
add_action( 'wp', 'unit_update_schedule' );
/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function unit_update_schedule() {
    if ( ! wp_next_scheduled( 'unit_update_event' ) ) {
        wp_schedule_event( time(), 'hourly', 'unit_update_event');
    }
}

register_deactivation_hook( __FILE__, 'unit_update_deactivation' );
/**
 * On deactivation, remove all functions from the scheduled action hook.
 */
function unit_update_deactivation() {
    wp_clear_scheduled_hook( 'unit_update_event' );
}

add_action( 'unit_update_event', 'unit_rank_update_hourly' );
/**
 * On the scheduled action hook, run a function.
 */
function unit_rank_update_hourly() {
    //These are jsut starting values and will need to be adjusted... probably better to use 0.X instead of whole nubmers...
    //Section weights
    $STAT_WEIGHT = 0.45;
    $SKILL_WEIGHT = 0.35;
    $VOTE_WEIGHT = 0.2;
    //$NICHE_WEIGHT = 0.1;
    
    //Individual Stat Weights
    $ATK_WEIGHT = 0.5;
    $DEF_WEIGHT = 0.2;
    $HP_WEIGHT = 0.2;
    $REC_WEIGHT = 0.1;
    
    //Individual Skill Weights
    //$LDR_SKILL_WEIGHT = 0.05;
    //$SYN_WEIGHT = 0.05;
    $HIT_CNT_WEIGHT = 0.3;
    //$BB_WEIGHT = 0.05;
    $BB_FILL_WEIGHT = 0.15;
    $BB_HIT_WEIGHT = 0.2;
    //$SBB_WEIGHT = 0.05;
    $SBB_FILL_WEIGHT = 0.15;
    $SBB_HIT_WEIGHT = 0.2;
    
    //Determine the min and max of each stat
    $min_max = array(
        'max_hp' => 0,
        'min_hp' => 10000,
        'max_atk' => 0,
        'min_atk' => 10000,
        'max_def' => 0,
        'min_def' => 10000,
        'max_rec' => 0,
        'min_rec' => 10000,
        'max_hit_count' => 0, 
        'min_hit_count' => 100,
        'max_bb_fill' => 0,
        'min_bb_fill' => 0,
        'max_sbb_fill' => 0,
        'min_sbb_fill' => 0,
        'max_bb_hits' => 0,
        'min_bb_hits' => 0,
        'max_sbb_hits' => 0,
        'min_sbb_hits' => 0,
        'max_votes' => 0,
        'min_votes' => 1000,
    );

    //Min Max determination
    $args = array(
        'post_type'     => 'unit',
        'posts_per_page' => -1
    );
    $units = new WP_Query( $args );
    if( $units->have_posts() ) : 
        while( $units->have_posts() ) : $units->the_post();
            $min_max['max_hp'] = (get_field('lord_hp') > $min_max['max_hp']) ? get_field('lord_hp') : $min_max['max_hp'];
            $min_max['min_hp'] = (get_field('lord_hp') < $min_max['min_hp']) ? get_field('lord_hp') : $min_max['min_hp'];
            $min_max['max_atk'] = (get_field('lord_atk') > $min_max['max_atk']) ? get_field('lord_atk') : $min_max['max_atk'];
            $min_max['min_atk'] = (get_field('lord_atk') < $min_max['min_atk']) ? get_field('lord_atk') : $min_max['min_atk'];
            $min_max['max_def'] = (get_field('lord_def') > $min_max['max_def']) ? get_field('lord_def') : $min_max['max_def'];
            $min_max['min_def'] = (get_field('lord_def') < $min_max['min_def']) ? get_field('lord_def') : $min_max['min_def'];
            $min_max['max_rec'] = (get_field('lord_rec') > $min_max['max_rec']) ? get_field('lord_rec') : $min_max['max_rec'];
            $min_max['min_rec'] = (get_field('lord_rec') < $min_max['min_rec']) ? get_field('lord_rec') : $min_max['min_rec'];
            $min_max['max_hit_count'] = (get_field('hit_count') > $min_max['max_hit_count']) ? get_field('hit_count') : $min_max['max_hit_count'];
            $min_max['min_hit_count'] = (get_field('hit_count') < $min_max['min_hit_count']) ? get_field('hit_count') : $min_max['min_hit_count'];
            //if( (int)get_field('bb_fill') > 0 ){
                $min_max['max_bb_fill'] = (get_field('bb_fill') > $min_max['max_bb_fill']) ? get_field('bb_fill') : $min_max['max_bb_fill'];
                //$min_max['min_bb_fill'] = (get_field('bb_fill') < $min_max['min_bb_fill']) ? get_field('bb_fill') : $min_max['min_bb_fill'];
            //}
            //if( (int)get_field('sbb_fill') > 0 ){
                $min_max['max_sbb_fill'] = (get_field('sbb_fill') > $min_max['max_sbb_fill']) ? get_field('sbb_fill') : $min_max['max_sbb_fill'];
                //$min_max['min_sbb_fill'] = (get_field('sbb_fill') < $min_max['min_sbb_fill']) ? get_field('sbb_fill') : $min_max['min_sbb_fill'];
            //}
            //if( (int)get_field('bb_hits') > 0 ){
                $min_max['max_bb_hits'] = (get_field('bb_hits') > $min_max['max_bb_hits']) ? get_field('bb_hits') : $min_max['max_bb_hits'];
                //$min_max['min_bb_hits'] = (get_field('bb_hits') < $min_max['min_bb_hits']) ? get_field('bb_hits') : $min_max['min_bb_hits'];
            //}
            //if( (int)get_field('sbb_hits') > 0 ){
                $min_max['max_sbb_hits'] = (get_field('sbb_hits') > $min_max['max_sbb_hits']) ? get_field('sbb_hits') : $min_max['max_sbb_hits'];
                //$min_max['min_sbb_hits'] = (get_field('sbb_hits') < $min_max['min_sbb_hits']) ? get_field('sbb_hits') : $min_max['min_sbb_hits'];
            //}
            $votes = (int)get_field('upvotes') - (int)get_field('downvotes');
            $min_max['max_votes'] = ( $votes > $min_max['max_votes']) ? $votes : $min_max['max_votes'];
            $min_max['min_votes'] = ( $votes < $min_max['min_votes']) ? $votes : $min_max['min_votes'];
                
        endwhile;
    endif;
    wp_reset_postdata();
    
    //Calculate the weighted value of the units
    if( $units->have_posts() ) : 
        while( $units->have_posts() ) : $units->the_post();
            $votes = get_field('upvotes') - get_field('downvotes');
            $weighted_value = ( (
                    (normalize((int)get_field('lord_atk'), $min_max['max_atk'], $min_max['min_atk']) * $ATK_WEIGHT) + 
                    (normalize((int)get_field('lord_def'), $min_max['max_def'], $min_max['min_def']) * $DEF_WEIGHT) +
                    (normalize((int)get_field('lord_hp'), $min_max['max_hp'], $min_max['min_hp']) * $HP_WEIGHT) + 
                    (normalize((int)get_field('lord_rec'), $min_max['max_rec'], $min_max['min_rec']) * $REC_WEIGHT)
                ) * $STAT_WEIGHT ) +
                ( (
                    //These skills are not available in the database yet...
                    //(get_field('ldr_skill_value') * $LDR_SKILL_WEIGHT) + (get_field('synergy_val') * $SYN_WEIGHT) +
                    (normalize((int)get_field('hit_count'), $min_max['max_hit_count'], $min_max['min_hit_count']) * $HIT_CNT_WEIGHT) + 
                    (normalize((int)get_field('bb_fill'), $min_max['max_bb_fill'], $min_max['min_bb_fill'], true) * $BB_FILL_WEIGHT) + 
                    (normalize((int)get_field('sbb_fill'), $min_max['max_sbb_fill'], $min_max['min_sbb_fill'], true) * $SBB_FILL_WEIGHT) + 
                    (normalize((int)get_field('bb_hits'), $min_max['max_bb_hits'], $min_max['min_bb_hits']) * $BB_HIT_WEIGHT) +
                    (normalize((int)get_field('sbb_hits'),  $min_max['max_sbb_hits'], $min_max['min_sbb_hits']) * $SBB_HIT_WEIGHT)
                ) * $SKILL_WEIGHT ) +
                ( normalize((int)$votes, $min_max['max_votes'], $min_max['min_votes']) * $VOTE_WEIGHT);
            //Add niche later...;
            //Update the weighted value of the unit
            update_field('weighted_value', $weighted_value, get_the_ID());
            
        endwhile;
    endif;
    wp_reset_postdata();
    
    //Set the updated rank adn tier of the unit
    $args = array(
        'post_type'     => 'unit',
        'meta_key'      => 'weighted_value',
        'orderby'       => 'meta_value_num',
        'order'         => 'DESC',
        'posts_per_page' => -1
    );
    $units = new WP_Query( $args );
    if( $units->have_posts() ) : 
        $rank = 1;
        $count = wp_count_posts('unit')->publish;
        while( $units->have_posts() ) : $units->the_post();
            update_field('rank', $rank, get_the_ID());
            if($rank / $count < 0.09){
                $tier = "Godly Tier";
            }elseif($rank / $count < 0.19){
                $tier = "Borderline 1 Tier";
            }elseif($rank / $count < 0.3){
                $tier = "Tier 1";
            }elseif($rank / $count < 0.42){
                $tier = "Borderline 2 Tier";
            }elseif($rank / $count < 0.62){
                $tier = "Tier 2";
            }elseif($rank / $count < 0.77){
                $tier = "Borderline 3 Tier";
            }else{
                $tier = "Tier 3";
            }
            update_field('tier', $tier, get_the_ID());
            $rank++;
        endwhile;
    endif;
    wp_reset_postdata();
}

function normalize($value, $max_range, $min_range, $invert = false){
    //this funciton normalizes the stats so it falls into a value between 1 and 10
    //UNLESS 0 is submitted which will return 0
    if($value == 0 || !is_int($value) ){
        return 0;
    }
    if($min_range == "" || !is_nan($min_range)){
        $min_rnage = 0;
    }
    $value =  1 + ($value - $min_range) * ( 100-1 ) / ($max_range - $min_range);
    if($invert){
        $value = -$value + 100; 
    }
    return $value;
}