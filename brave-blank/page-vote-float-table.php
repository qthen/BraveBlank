<?php
/*
Template Name: Vote Float Table
*/
	
	$DEVELOPMENT_MODE = false;
	$VERSION = "0.0.8";
	//Kuhaku's voting stuff little modded:
	if (isset($_GET['id'])) {
		//This means that the user has voted so check first if they voted prior to 24 hours
		$id = $_GET['id'];
		$action = $_GET['action'];
		
		if (isset($_COOKIE['units'])) {
			$unit_array = explode(',', $_COOKIE['units']);
		}else{
			$unit_array = array();
		}
		
		if (!in_array($id, $unit_array)) {
			//Only if the user has not voted in the past 24 hours for this unit
			$id = $_GET['id'];
			$action = $_GET['action'];
			if ($action == 1) {
				//The user is upvoting so cast the upvote
				$upvotes = get_field('upvotes', $id) + 1;
				update_field('field_54999edc9611e', $upvotes, $id);
			}
			else {
				//The user is downvoting
				$downvotes = get_field('downvotes', $id) + 1;
				update_field('field_54999f2b9611f', $downvotes, $id);
			}
			array_push($unit_array, $id);
			$unit_array = implode(',', $unit_array);
			setcookie('units', $unit_array, time() + 43200);
		}
	}

	if (isset($_GET['dev'])) {
		if($_GET['dev'] == "true"){
			$DEVELOPMENT_MODE = true;
		}
	}
	get_header();
	global $woo_options;
	
	if($DEVELOPMENT_MODE):
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
	
	if ( ! function_exists( 'normalize' ) ) {
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
	}

	if ( ! function_exists( 'acf_field_key' ) ) {
		function acf_field_key($params = array(), $field_name) {
			
			if (empty($params) or !isset($params['fields'])) {
				
				return $field_name;
			}
			
			// Loop and find the value
			foreach ($params['fields'] as $field) {
				
				if ($field['name'] == $field_name) {
					
					return $field['key'];
				}
			}
		}
	}

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

	$args = array(
		'post_type'		=> 'unit',
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
	//Debugging
	echo "Version: " . $VERSION . "<br/>";
	var_dump($min_max);
	echo "<br/><br/>";

	//echo get_field('upvotes', 349) - get_field('downvotes', 349) . "<br>";
	//Calculate the weighted value of the units
	if( $units->have_posts() ) : 
		while( $units->have_posts() ) : $units->the_post();
			$votes = get_field('upvotes') - get_field('downvotes');
			$weighted_value = ( (
					(normalize((int)get_field('lord_atk'), $min_max['max_atk'], $min_max['min_atk']) * $ATK_WEIGHT) + 
					(normalize((int)get_field('lord_def'), $min_max['max_def'], $min_max['min_def']) * $DEF_WEIGHT) +
					(normalize((int)get_field('lord_hp'), $min_max['max_hp'], $min_max['min_hp']) * $HP_WEIGHT) + 
					(normalize((int)get_field('lord_rec'), $min_max['max_rec'], $min_max['min_rec']) * $REC_WEIGHT)
				) *	$STAT_WEIGHT ) +
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
			update_field('field_54999fdb96120', $weighted_value, get_the_ID());
			
			if (get_the_ID() == 428){
				echo "Scores for Cyclopean Ultor<br/>";
				echo "Raw Attack Score: " . normalize((int)get_field('lord_atk'), $min_max['max_atk'], $min_max['min_atk']) ."<br/>";
				echo "Raw Defense Score: " . normalize((int)get_field('lord_def'), $min_max['max_def'], $min_max['min_def']) ."<br/>";
				echo "Raw HP Score: " . normalize((int)get_field('lord_hp'), $min_max['max_hp'], $min_max['min_hp']) ."<br/>";
				echo "Raw Recovery Score: " . normalize((int)get_field('lord_rec'), $min_max['max_rec'], $min_max['min_rec']) ."<br/>";
				echo "Raw Hit Count Score: " . normalize((int)get_field('hit_count'), $min_max['max_hit_count'], $min_max['min_hit_count']) ."<br/>";
				echo "Raw BB Hit Score: " . normalize((int)get_field('bb_hits'), $min_max['max_bb_hits'], $min_max['min_bb_hits']) ."<br/>";
				echo "Raw BB Fill Score: " . normalize((int)get_field('bb_fill'), $min_max['max_bb_fill'], $min_max['min_bb_fill'], true) ."<br/>";
				echo "Raw SBB Hit Score: " . normalize((int)get_field('sbb_hits'),  $min_max['max_sbb_hits'], $min_max['min_sbb_hits']) ."<br/>";
				echo "Raw SBB Fill Score: " . normalize((int)get_field('sbb_fill'), $min_max['max_sbb_fill'], $min_max['min_sbb_fill'], true) ."<br/>";
				echo "Raw Votes Score: " . normalize((int)$votes, $min_max['max_votes'], $min_max['min_votes']) ."<br/>";
				echo "Weighted: " . $weighted_value ."<br/>";
				echo "Stat Score: " . ((
					(normalize((int)get_field('lord_atk'), $min_max['max_atk'], $min_max['min_atk']) * $ATK_WEIGHT) + 
					(normalize((int)get_field('lord_def'), $min_max['max_def'], $min_max['min_def']) * $DEF_WEIGHT) +
					(normalize((int)get_field('lord_hp'), $min_max['max_hp'], $min_max['min_hp']) * $HP_WEIGHT) + 
					(normalize((int)get_field('lord_rec'), $min_max['max_rec'], $min_max['min_rec']) * $REC_WEIGHT)
				) *	$STAT_WEIGHT) . "<br/>";
				echo "Skill Score: " . ( (
					//These skills are not available in the database yet...
					//(get_field('ldr_skill_value') * $LDR_SKILL_WEIGHT) + (get_field('synergy_val') * $SYN_WEIGHT) +
					(normalize((int)get_field('hit_count'), $min_max['max_hit_count'], $min_max['min_hit_count']) * $HIT_CNT_WEIGHT) + 
					(normalize((int)get_field('bb_fill'), $min_max['max_bb_fill'], $min_max['min_bb_fill'], true) * $BB_FILL_WEIGHT) + 
					(normalize((int)get_field('sbb_fill'), $min_max['max_sbb_fill'], $min_max['min_sbb_fill'], true) * $SBB_FILL_WEIGHT) + 
					(normalize((int)get_field('bb_hits'), $min_max['max_bb_hits'], $min_max['min_bb_hits']) * $BB_HIT_WEIGHT) +
					(normalize((int)get_field('sbb_hits'),  $min_max['max_sbb_hits'], $min_max['min_sbb_hits']) * $SBB_HIT_WEIGHT)
				) * $SKILL_WEIGHT ) . "<br/>";
			}
			
		endwhile;
	endif;
	wp_reset_postdata();
	
	//Set the updated rank of the unit
	$args = array(
		'post_type'		=> 'unit',
		'meta_key'		=> 'weighted_value',
		'orderby'		=> 'meta_value_num',
		'order'			=> 'DESC',
		'posts_per_page' => -1
	);
	$units = new WP_Query( $args );
	if( $units->have_posts() ) : 
		$rank = 1;
		$count = wp_count_posts('unit')->publish;
		while( $units->have_posts() ) : $units->the_post();
			update_field('field_54999c5b9611c', $rank, get_the_ID());
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
			update_field('field_54a190f64b9fa', $tier, get_the_ID());
			$rank++;
		endwhile;
	endif;
	wp_reset_postdata();

	endif; //END IF FOR DEVELOPMENT MODE
	//Default full width display for pages in this theme.  
    //We display the content from the post first then display the float table after words.
?>
    <div id="content" class="page col-full">
		<section id="main" class="fullwidth">
		<?php if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) { ?>
			<section id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</section><!--/#breadcrumbs -->
		<?php } ?>
        <?php
        	if ( have_posts() ) { $count = 0;
        		while ( have_posts() ) { the_post(); $count++;
        ?>                                                             
                <article <?php post_class(); ?>>
					<header>
						<h1><?php the_title(); ?></h1>
					</header>
                    <section class="entry">
	                	<?php the_content(); ?>
	               	</section><!-- /.entry -->
					<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
                </article><!-- /.post -->               
			<?php
					} // End WHILE Loop
				} else {
			?>
				<article <?php post_class(); ?>>
                	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
                </article><!-- /.post -->
            <?php } ?>  
            <!-- Display the table -->
        	<table id="tablepress-19" class="tablepress tablepress-id-19">
				<thead>
					<tr class="row-1 odd">
						<th class="column-1"><div>Overall Ranking</div></th>
                        <th class="column-2"><div>Unit</div></th>
                        <th class="column-3"><div>Tier</div></th>
                        <th class="column-4"><div>Element</div></th>
                        <th class="column-5"><div>HP</div></th>
                        <th class="column-6"><div>ATK</div></th>
                        <th class="column-7"><div>DEF</div></th>
                        <th class="column-8"><div>REC</div></th>
                        <th class="column-9"><div>Weighted Value</div></th>
                        <th class="column-10"><div>Today's Votes</div></th>
					</tr>
				</thead>
				<tbody class="row-hover">
            <?php
			//Display the rows...
			wp_reset_postdata();
			$args = array(
				'post_type'		=> 'unit',
				'meta_query'	=> array(
					array(
						'key'		=> 'rarity',
						'value'		=> 4,
						'type'		=> 'NUMERIC',
						'compare'	=> '>'
					)
				),
				'meta_key'		=> 'rank',
				'orderby'		=> 'meta_value_num',
				'order'			=> 'ASC',
				'posts_per_page' => -1
			);
			$units = new WP_Query( $args );
			if( $units->have_posts() ) :
				$row_count = 1; 
				while( $units->have_posts() ) : $units->the_post();
				 ?>
                <tr class="row-<?php echo $row_count + ($row_count%2 == 0 ? " even" : " odd"); ?>" >
                	<td class = "column-1" width = "13%">
                    	<?php if (get_field('rank') == 1): ?>
                        <img src = "http://www.braveblank.com/wp-content/uploads/2014/12/goldstar.png" height = "20" width = "20"></img>&nbsp;
                        <? else: ?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<?php endif; ?>
                        <?php if(get_field('rank') > get_field('last_weeks_rank')): ?>
                        <img src="http://www.braveblank.com/wp-content/uploads/2014/12/up.png" height="40" width = "30"></img>
                        <? elseif(get_field('rank') == get_field('last_weeks_rank')): ?>
						<img src="http://www.braveblank.com/wp-content/uploads/2014/12/same.png" height="40" width = "30"></img>
                        <? else: ?>
						<img src="http://www.braveblank.com/wp-content/uploads/2014/12/down.png" height="40" width = "30"></img>
						<?php endif; ?>
                    	<font size="6" color = "black"><?php the_field('rank');?></font>
                        <font size="3"><hr>&nbsp;&nbsp;Last Week:<?php the_field('last_weeks_rank');?></font>
                    </td>
                    <!-- add in a link to the custom post type single display when complete -->
                    <td class="column-2">
                    	<?php the_post_thumbnail( array(50, 50) ); ?>
                    	<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                    </td>
                    <td class="column-3"><?php the_field('tier');?></td>
                    <td class="column-4"><?php the_field('element');?></td>
                    <td class="column-5"><?php the_field('lord_hp');?></td>
                    <td class="column-6"><?php the_field('lord_atk');?></td>
                    <td class="column-7"><?php the_field('lord_def');?></td>
                    <td class="column-8"><?php the_field('lord_rec');?></td>
                    <td class="column-9"><?php the_field('weighted_value');?></td>
                    <td class="column-10" width = "10%">
                    	<a href=".?id=<?php the_ID(); ?>&action=1"><?php the_field('upvotes');?>&nbsp;Upvotes</a><hr>
                        <a href=".?id=<?php the_ID(); ?>&action=2"><?php the_field('downvotes');?>&nbsp;Downvotes</a>
                    </td>
                </tr>
            <?php
					$row_count++;
				endwhile;
			endif;
			
            ?>
            	</tbody>
            </table>
		</section><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>