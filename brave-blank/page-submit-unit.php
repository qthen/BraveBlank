<?php
/**
 * Single Post Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a post ('post' post_type).
 * @link http://codex.wordpress.org/Post_Types#Post
 *
 * @package WooFramework
 * @subpackage Template
 */
class foo{
}

if( isset($_GET['post_id'])){
	$post_data = get_post(intval($_GET['post_id']));
	$unit_data = get_fields(intval($_GET['post_id']));
}elseif( isset($_POST['title']) ){
	$unit = new unitModel;
	$unit_data = $unit->create_update_unit($_POST);
	$post_data = new foo;
}else{
	$unit = new unitModel;
	$unit_data = $unit->unit_data;
	$post_data = new foo;
}

get_header();
global $woo_options;
/**
 * The Variables
 *
 * Setup default variables, overriding them if the "Theme Options" have been saved.
 */
$settings = array(
	'thumb_single' => 'false', 
	'single_w' => 200, 
	'single_h' => 200, 
	'thumb_single_align' => 'alignright'
	);
$settings = woo_get_dynamic_values( $settings );
$embed_width = 760;
if ( woo_active_sidebar( 'secondary' ) ) { $embed_width = '500'; }
?>
<div id="content" class="col-full">
	<section id="main" class="col-right">
		<?php if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) : ?>
		<section id="breadcrumbs">
			<?php woo_breadcrumbs(); ?>
		</section><!--/#breadcrumbs -->
		<?php endif; ?>
		<?php
		if ( have_posts() ) :
			$count = 0;
			while ( have_posts() ) :
				the_post(); 
				$count++;
				?>
				<article <?php post_class(); ?>>
					<header>
						<h1><?php the_title(); ?></h1>
						<?php if ( array_key_exists('errors', $unit_data) ): if(count($unit_data['errors']) != 0): foreach($unit_data['errors'] as $error){?>
								<p class="error warning"><?php echo $error; ?></p>
						<?php } endif; endif; ?>
						<?php if ( array_key_exists('success', $unit_data) ): ?>
								<p class="success"><?php echo $unit_data['success']; ?></p>
						<?php endif; ?>
						<?php woo_post_meta(); ?>
					</header>
					<div class="fix"></div>
					<?php echo woo_embed( 'width=' . $embed_width ); ?>
					<?php
					if ( $settings['thumb_single'] == 'true' ) {
						$image = woo_image( 'return=true&width=' . $settings['single_w'] . '&height=' . $settings['single_h'] . '&link=img&class=thumbnail' );
						
						if ( $image != '' ) {
							?>
							<?php if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] != 'content' ) { ?>
							<div class="drop-shadow curved curved-hz-1 <?php echo $settings['thumb_single_align']; ?>">
								<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" style="height: <?php echo $settings['single_h']; ?>px;">
									<?php echo $image; ?>
								</a>
							</div><!--/.drop-shadow-->
							<?php
						}
					}
				}
				?>
				<section class="entry">
					<form method="post" action="">
	                	<h1>Unit Name <input type="text" name="title" value="<?php if( property_exists($post_data, 'post_title') ){echo $post_data->post_title;} ?>"></h1>
	                	<table id="tablepress-34" class="tablepress tablepress-id-34">
	                		<tbody class="row-hover">
	                			<tr class="row-1 odd">
	                				<td class="column-1">No.</td>
	                				<td class="column-2"><input type="number" name="unit_number" maxlength="4" max="9000" min="1" value="<?php echo $unit_data['unit_number']; ?>"></td>
	                			</tr>
	                			<tr class="row-2 even alt-table-row">
	                				<td class="column-1">Element</td>
	                				<td class="column-2">
	                					<select name="element">
	                						<option value="Fire" <?php echo ($unit_data['element'] == "Fire") ? "selected" : "" ; ?>>Fire</option>
	                						<option value="Water" <?php echo ($unit_data['element'] == "Water") ? "selected" : "" ; ?>>Water</option>
	                						<option value="Earth" <?php echo ($unit_data['element'] == "Earth") ? "selected" : "" ; ?>>Earth</option>
	                						<option value="Thunder" <?php echo ($unit_data['element'] == "Thunder") ? "selected" : "" ; ?>>Thunder</option>
	                						<option value="Light" <?php echo ($unit_data['element'] == "Light") ? "selected" : "" ; ?>>Light</option>
	                						<option value="Dark" <?php echo ($unit_data['element'] == "Dark") ? "selected" : "" ; ?>>Dark</option>
	                					</select>
	                				</td>
	                			</tr>
	                			<tr class="row-3 odd">
	                				<td class="column-1">Rarity</td>
	                				<td class="column-2"><input type="number" name="rarity" maxlength="1" max="6" min="1" value="<?php echo $unit_data['rarity']; ?>">*</td>
	                			</tr>
	                			<tr class="row-4 even alt-table-row">
	                				<td class="column-1">Max Lv.</td>
	                				<td class="column-2"><input type="number" name="max_level" maxlength="3" max="100" min="1" value="<?php echo $unit_data['max_level']; ?>"></td>
	                			</tr>
	                			<tr class="row-5 odd">
	                				<td class="column-1">Cost</td>
	                				<td class="column-2"><input type="number" name="cost" maxlength="2" max="50" min="1" value="<?php echo $unit_data['cost']; ?>"></td>
	                			</tr>
	                			<tr class="row-6 even alt-table-row">
	                				<td class="column-1">Hit Count</td>
	                				<td class="column-2"><input type="number" name="hit_count" maxlength="2" max="50" min="1" value="<?php echo $unit_data['hit_count']; ?>"></td>
	                			</tr>
	                			<tr class="row-7 odd">
	                				<td class="column-1">BB Hits</td>
	                				<td class="column-2"><input type="number" name="bb_hits" maxlength="2" max="50" min="0" value="<?php echo $unit_data['bb_hits']; ?>"></td>
	                			</tr>
	                			<tr class="row-8 even alt-table-row">
	                				<td class="column-1">SBB Hits</td>
	                				<td class="column-2"><input type="number" name="sbb_hits" maxlength="2" max="50" min="0" value="<?php echo $unit_data['sbb_hits']; ?>"></td>
	                			</tr>
	                			<tr class="row-9 odd">
	                				<td class="column-1">BB Fill</td>
	                				<td class="column-2"><input type="number" name="bb_fill" maxlength="2" max="100" min="0" value="<?php echo $unit_data['bb_fill']; ?>"></td>
	                			</tr>
	                			<tr class="row-10 even alt-table-row">
	                				<td class="column-1">SBB Fill</td>
	                				<td class="column-2"><input type="number" name="sbb_fill" maxlength="2" max="100" min="0" value="<?php echo $unit_data['sbb_fill']; ?>"></td>
	                			</tr>
	                		</tbody>
	                	</table>
	                	<table id="tablepress-31" class="tablepress tablepress-id-31">
	                		<thead>
	                			<tr class="row-1 odd">
	                				<th class="column-1"><div>Type</div></th>
	                				<th class="column-2"><div>HP</div></th>
	                				<th class="column-3"><div>ATK</div></th>
	                				<th class="column-4"><div>DEF</div></th>
	                				<th class="column-5"><div>REC</div></th>
	                			</tr>
	                		</thead>
	                		<tbody class="row-hover">
	                			<tr class="row-2 even alt-table-row">
	                				<td class="column-1">Lord</td>
	                				<td class="column-2"><input type="number" name="lord_hp" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['lord_hp']; ?>"></td>
	                				<td class="column-3"><input type="number" name="lord_atk" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['lord_atk']; ?>"></td>
	                				<td class="column-4"><input type="number" name="lord_def" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['lord_def']; ?>"></td>
	                				<td class="column-5"><input type="number" name="lord_rec" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['lord_rec']; ?>"></td>
	                			</tr>
	                			<tr class="row-3 odd">
	                				<td class="column-1">Anima</td>
	                				<td class="column-2"><input type="number" name="anima_hp" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['anima_hp']; ?>"></td>
	                				<td class="column-3"><input type="number" name="anima_atk" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['anima_hp']; ?>"></td>
	                				<td class="column-4"><input type="number" name="anima_def" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['anima_hp']; ?>"></td>
	                				<td class="column-5"><input type="number" name="anima_rec" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['anima_hp']; ?>"></td>
	                			</tr>
	                			<tr class="row-4 even alt-table-row">
	                				<td class="column-1">Breaker</td>
	                				<td class="column-2"><input type="number" name="breaker_hp" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['breaker_hp']; ?>"></td>
	                				<td class="column-3"><input type="number" name="breaker_atk" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['breaker_hp']; ?>"></td>
	                				<td class="column-4"><input type="number" name="breaker_def" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['breaker_hp']; ?>"></td>
	                				<td class="column-5"><input type="number" name="breaker_rec" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['breaker_hp']; ?>"></td>
	                			</tr>
	                			<tr class="row-5 odd">
	                				<td class="column-1">Guardian</td>
	                				<td class="column-2"><input type="number" name="guardian_hp" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['guardian_hp']; ?>"></td>
	                				<td class="column-3"><input type="number" name="guardian_atk" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['guardian_hp']; ?>"></td>
	                				<td class="column-4"><input type="number" name="guardian_def" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['guardian_hp']; ?>"></td>
	                				<td class="column-5"><input type="number" name="guardian_rec" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['guardian_hp']; ?>"></td>
	                			</tr>
	                			<tr class="row-6 even alt-table-row">
	                				<td class="column-1">Oracle</td>
	                				<td class="column-2"><input type="number" name="oracle_hp" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['oracle_hp']; ?>"></td>
	                				<td class="column-3"><input type="number" name="oracle_atk" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['oracle_hp']; ?>"></td>
	                				<td class="column-4"><input type="number" name="oracle_def" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['oracle_hp']; ?>"></td>
	                				<td class="column-5"><input type="number" name="oracle_rec" maxlength="5" max="10000" min="1" value="<?php echo $unit_data['oracle_hp']; ?>"></td>
	                			</tr>
	                			<tr class="row-7 odd">
	                				<td class="column-1">Max Bonus</td>
	                				<td class="column-2">
	                					<img data-id="1423" src="http://www.braveblank.com/wp-content/uploads/2014/12/20.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1423">+ 
	                					<input type="number" name="max_hp_bonus" maxlength="3" max="999" min="1" value="<?php echo $unit_data['max_hp_bonus']; ?>">
	                				</td>
	                				<td class="column-3">
	                					<img data-id="1422" src="http://www.braveblank.com/wp-content/uploads/2014/12/20-1.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1422">+ 
	                					<input type="number" name="max_atk_bonus" maxlength="3" max="999" min="1" value="<?php echo $unit_data['max_atk_bonus']; ?>">
	                				</td>
	                				<td class="column-4">
	                					<img data-id="1421" src="http://www.braveblank.com/wp-content/uploads/2014/12/20-2.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1421">+ 
	                					<input type="number" name="max_def_bonus" maxlength="3" max="999" min="1" value="<?php echo $unit_data['max_def_bonus']; ?>">
	                				</td>
	                				<td class="column-5">
	                					<img data-id="1420" src="http://www.braveblank.com/wp-content/uploads/2014/12/20-3.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1420">+ 
	                					<input type="number" name="max_rec_bonus" maxlength="3" max="999" min="1" value="<?php echo $unit_data['max_rec_bonus']; ?>">
	                				</td>
	                			</tr>
	                		</tbody>
	                	</table>
	                	<table id="tablepress-32" class="tablepress tablepress-id-32">
	                		<caption style="caption-side:bottom;text-align:left;border:none;background:none;margin:0;padding:0;"><a href="http://www.braveblank.com/wp-admin/admin.php?page=tablepress&amp;action=edit&amp;table_id=32"></a></caption>
	                		<thead>
	                			<tr class="row-1 odd alt-table-row">
	                				<th class="column-1">
	                					<div>Skill</div>
	                				</th>
	                				<th class="column-2">
	                					<div>Effect</div>
	                				</th>
	                			</tr>
	                		</thead>
	                		<tbody class="row-hover">
	                			<tr class="row-2 even">
	                				<td class="column-1">
	                					<strong>Leader Skill: <input type="text" name="leader_skill" value="<?php echo $unit_data['leader_skill']; ?>"></strong>
	                				</td>
	                				<td class="column-2"><input type="text" name="leaders_skill_effect" value="<?php echo $unit_data['leaders_skill_effect']; ?>"></td>
	                			</tr>
	                			<tr class="row-3 odd alt-table-row">
	                				<td class="column-1">
	                					<strong>Brave Burst: <input type="text" name="brave_burst" value="<?php echo $unit_data['brave_burst']; ?>"></strong>
	                				</td>
	                				<td class="column-2"><input type="text" name="brave_burst_effect" value="<?php echo $unit_data['brave_burst_effect']; ?>"></td>
	                			</tr>
	                			<tr class="row-4 even">
	                				<td class="column-1">
	                					<strong>Super Brave Burst: <input type="text" name="super_brave_burst" value="<?php echo $unit_data['super_brave_burst']; ?>"></strong>
	                				</td>
	                				<td class="column-2"><input type="text" name="super_brave_burst_effect" value="<?php echo $unit_data['super_brave_burst_effect']; ?>"></td>
	                			</tr>
	                		</tbody>
	                	</table>
						<strong>Unit Story:</strong><br/>
						<textarea name="description" cols="50" rows="10"><?php if( property_exists($post_data, 'post_content') ){echo $post_data->post_content;} ?></textarea><br/>
						<input type="submit" value="Submit">
					</form>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'braveblank' ), 'after' => '</div>' ) ); ?>
				</section>
				<?php the_tags( '<p class="tags">'.__( 'Tags: ', 'braveblank' ), ', ', '</p>' ); ?>
			</article><!-- .post -->
			<?php if ( isset( $woo_options['woo_post_author'] ) && $woo_options['woo_post_author'] == 'true' ) : ?>
			<aside id="post-author" class="fix">
				<div class="profile-image"><?php echo get_avatar( get_the_author_meta( 'ID' ), '70' ); ?></div>
				<div class="profile-content">
					<h3 class="title"><?php printf( esc_attr__( 'About %s', 'braveblank' ), get_the_author() ); ?></h3>
					<?php the_author_meta( 'description' ); ?>
					<div class="profile-link">
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'braveblank' ), get_the_author() ); ?>
						</a>
					</div><!-- #profile-link	-->
				</div><!-- .post-entries -->
			</aside><!-- #post-author -->
			<?php endif; ?>
			<?php woo_subscribe_connect(); ?>
			<nav id="post-entries" class="fix">
				<div class="nav-prev fl"><?php previous_post_link( '%link', '<span class="meta-nav">&larr;</span> %title' ); ?></div>
				<div class="nav-next fr"><?php next_post_link( '%link', '%title <span class="meta-nav">&rarr;</span>' ); ?></div>
			</nav><!-- #post-entries -->
			<?php
            	// Determine wether or not to display comments here, based on "Theme Options".
			if ( isset( $woo_options['woo_comments'] ) && in_array( $woo_options['woo_comments'], array( 'post', 'both' ) ) ) :
				comments_template();
			endif;
			endwhile;
		else :?>
			<article <?php post_class(); ?>>
				<p><?php _e( 'Sorry, no units matched your criteria.', 'braveblank' ); ?></p>
			</article><!-- .post -->             
		<?php endif; ?>  
			</section><!-- #main -->
			<?php get_sidebar(); ?>
		</div><!-- #content -->
		
		<?php get_footer(); ?>