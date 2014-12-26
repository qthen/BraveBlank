<?php
/*
Template Name: Vote Float Table
Version: 0.0.4
*/
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
				$upvotes = get_field('upvotes') + 1;
				update_field('upvotes', $upvotes, $id);
			}
			else {
				//The user is downvoting
				$downvotes = get_field('downvotes') + 1;
				update_field('downvotes', $downvotes, $id);
			}
			array_push($unit_array, $id);
			$unit_array = implode(',', $unit_array);
			setcookie('units', $unit_array, time() + 43200);
		}
	}
	get_header();
	global $woo_options;
	
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
	*/
	
	//These are jsut starting values and will need to be adjusted... probably better to use 0.X instead of whole nubmers...
	//Section weights
	$STAT_WEIGHT = 0.6;
	$SKILL_WEIGHT = 0.3;
	$VOTE_WEIGHT = 0.2;
	$NICHE_WEIGHT = 0.1;
	
	//Individual Stat Weights
	$ATK_WEIGHT = 0.6;
	$DEF_WEIGHT = 0.3;
	$HP_WEIGHT = 0.3;
	$REC_WEIGHT = 0.1;
	
	//Individual Skill Weights
	$LDR_SKILL_WEIGHT = 0.2;
	$SYN_WEIGHT = 0.2;
	$HIT_CNT_WEIGHT = 0.2;
	$BB_WEIGHT = 0.2;
	$BB_FILL_WEIGHT = 0.2;
	
	function normalize($value, $max_range, $min_range){
		//this funciton normalizes the stats so it falls into a value between 1 and 10
		//UNLESS 0 is submitted which will return 0
		if($value == 0 || !is_int($value) ){
			return 0;
		}
		return 1 + ($value - $min_range) * ( 10-1 ) / ($max_range - $min_range);
	}
	
	//Calculate the weighted value of the units
	$args = array(
		'post_type'		=> 'unit',
		'posts_per_page' => -1
	);
	$units = new WP_Query( $args );
	if( $units->have_posts() ) : 
		while( $units->have_posts() ) : $units->the_post();
			
			$weighted_value = ( ((get_field('lord_atk') * $ATK_WEIGHT) + (get_field('lord_def') * $DEF_WEIGHT) +
			(get_field('lord_hp') * $HP_WEIGHT) + (get_field('lord_rec') * $REC_WEIGHT)) * $STAT_WEIGHT ) +
			( ((get_field('ldr_skill_value') * $LDR_SKILL_WEIGHT) + (get_field('synergy_val') * $SYN_WEIGHT) +
			(get_field('hit_count') * $HIT_CNT_WEIGHT) + (get_field('bb_fill') * $BB_FILL_WEIGHT) + 
			(get_field('bb_value') * $BB_WEIGHT)) * $SKILL_WEIGHT ) +
			( (get_field('upvotes') - get_field('downvotes') ) * $VOTE_WEIGHT);
			//Add niche later...;
			
			//Update the weighted value of the unit
			update_field('weighted_value', $weighted_value, get_the_ID());
		
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
		while( $units->have_posts() ) : $units->the_post();
			update_field('rank', $rank, get_the_ID());
			$rank++;
		endwhile;
	endif;
	wp_reset_postdata();
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
                    <td class="column-3">Add tiers...</td>
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