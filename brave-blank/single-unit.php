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

						<?php brave_blank_post_meta(); ?>

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
                	<h1><?php the_post_thumbnail( array(50, 50) ); the_title();?></h1>
                	<table id="tablepress-34" class="tablepress tablepress-id-34">
                		<tbody class="row-hover">
                			<tr class="row-1 odd">
                				<td class="column-1">No.</td>
                				<td class="column-2"><?php the_field('unit_number'); ?></td>
                			</tr>
                			<tr class="row-2 even alt-table-row">
                				<td class="column-1">Element</td>
                				<td class="column-2"><?php the_field('element'); ?></td>
                			</tr>
                			<tr class="row-3 odd">
                				<td class="column-1">Rarity</td>
                				<td class="column-2"><?php the_field('rarity'); ?> *</td>
                			</tr>
                			<tr class="row-4 even alt-table-row">
                				<td class="column-1">Max Lv.</td>
                				<td class="column-2"><?php the_field('max_level'); ?></td>
                			</tr>
                			<tr class="row-5 odd">
                				<td class="column-1">Cost</td>
                				<td class="column-2"><?php the_field('cost'); ?></td>
                			</tr>
                			<tr class="row-6 even alt-table-row">
                				<td class="column-1">Hit Count</td>
                				<td class="column-2"><?php the_field('hit_count'); ?></td>
                			</tr>
                			<tr class="row-7 odd">
                				<td class="column-1">BB fill</td>
                				<td class="column-2"><?php the_field('bb_fill'); ?></td>
                			</tr>
                			<tr class="row-8 even alt-table-row">
                				<td class="column-1">SBB fill</td>
                				<td class="column-2"><?php the_field('sbb_fill'); ?></td>
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
                				<td class="column-2"><?php the_field('lord_hp'); ?></td>
                				<td class="column-3"><?php the_field('lord_atk'); ?></td>
                				<td class="column-4"><?php the_field('lord_def'); ?></td>
                				<td class="column-5"><?php the_field('lord_rec'); ?></td>
                			</tr>
                			<tr class="row-3 odd">
                				<td class="column-1">Anima</td>
                				<td class="column-2"><?php the_field('anima_hp'); ?></td>
                				<td class="column-3"><?php the_field('anima_atk'); ?></td>
                				<td class="column-4"><?php the_field('anima_def'); ?></td>
                				<td class="column-5"><?php the_field('anima_rec'); ?></td>
                			</tr>
                			<tr class="row-4 even alt-table-row">
                				<td class="column-1">Breaker</td>
                				<td class="column-2"><?php the_field('breaker_hp'); ?></td>
                				<td class="column-3"><?php the_field('breaker_atk'); ?></td>
                				<td class="column-4"><?php the_field('breaker_def'); ?></td>
                				<td class="column-5"><?php the_field('breaker_rec'); ?></td>
                			</tr>
                			<tr class="row-5 odd">
                				<td class="column-1">Guardian</td>
                				<td class="column-2"><?php the_field('guardian_hp'); ?></td>
                				<td class="column-3"><?php the_field('guardian_atk'); ?></td>
                				<td class="column-4"><?php the_field('guardian_def'); ?></td>
                				<td class="column-5"><?php the_field('guardian_rec'); ?></td>
                			</tr>
                			<tr class="row-6 even alt-table-row">
                				<td class="column-1">Oracle</td>
                				<td class="column-2"><?php the_field('oracle_hp'); ?></td>
                				<td class="column-3"><?php the_field('oracle_atk'); ?></td>
                				<td class="column-4"><?php the_field('oracle_def'); ?></td>
                				<td class="column-5"><?php the_field('oracle_rec'); ?></td>
                			</tr>
                			<tr class="row-7 odd">
                				<td class="column-1">Max Bonus</td>
                				<td class="column-2">
                					<img data-id="1423" src="http://www.braveblank.com/wp-content/uploads/2014/12/20.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1423">+ <?php the_field('max_hp_bonus'); ?>
                				</td>
                				<td class="column-3">
                					<img data-id="1422" src="http://www.braveblank.com/wp-content/uploads/2014/12/20-1.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1422">+ <?php the_field('max_atk_bonus'); ?>
                				</td>
                				<td class="column-4">
                					<img data-id="1421" src="http://www.braveblank.com/wp-content/uploads/2014/12/20-2.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1421">+ <?php the_field('max_def_bonus'); ?>
                				</td>
                				<td class="column-5">
                					<img data-id="1420" src="http://www.braveblank.com/wp-content/uploads/2014/12/20-3.png" alt="" width="20" height="20" class="alignnone size-full wp-image-1420">+ <?php the_field('max_rec_bonus'); ?>
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
                					<strong>Leader Skill: <?php the_field('leader_skill'); ?></strong>
                				</td>
                				<td class="column-2"><?php the_field('leaders_skill_effect'); ?></td>
                			</tr>
                			<tr class="row-3 odd alt-table-row">
                				<td class="column-1">
                					<strong>Brave Burst: <?php the_field('brave_burst'); ?></strong>
                				</td>
                				<td class="column-2"><?php the_field('brave_burst_effect'); ?></td>
                			</tr>
                			<tr class="row-4 even">
                				<td class="column-1">
                					<strong>Super Brave Burst: <?php the_field('super_brave_burst'); ?></strong>
                				</td>
                				<td class="column-2"><?php the_field('super_brave_burst_effect'); ?></td>
                			</tr>
                		</tbody>
                	</table>
					<?php the_content(); ?>
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