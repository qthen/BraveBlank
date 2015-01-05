<?php
/*
Template Name: Team Builder
*/
	
	get_header();
	global $woo_options;
	
?>
    <div id="content" class="page col-full">
		<section id="main" class="fullwidth">
		<?php if ( isset( $woo_options['woo_breadcrumbs_show'] ) && $woo_options['woo_breadcrumbs_show'] == 'true' ) { ?>
			<section id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</section><!--/#breadcrumbs -->
		<?php } 

		if (is_user_logged_in()):
        	if ( have_posts() ) :
        		while ( have_posts() ) { the_post(); 
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
				else:
			?>
				<article <?php post_class(); ?>>
                	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
                </article><!-- /.post -->
            <?php endif;
            $nonce = wp_create_nonce("unit_search");
    		$link = admin_url('admin-ajax.php?action=unit_search&nonce='.$nonce);
    		?>
    		<h3>My Available Units</h3>
    		<div id="user_units">
    			<p>You have no units</p>
    		</div>
            <input id="unit_name" name="unit_name" type="text" placeholder="Unit Name">
            <button id="find_unit">Find</button>
            <div id="search_results"></div>
            <script>
            	jQuery(document).ready(function( $ ) {
					$('#find_unit').click(function(){
						unit_name = $('#unit_name').val();
						$.ajax({
					         type : "GET",
					         dataType : "json",
					         url : "<?php echo $link; ?>&q=" + unit_name,
					         success: function(response) {
					            if(response.type == "success") {
					            	for (var i = 0; i < response.units.length; i++){
					            		var rarity = parseInt(response.units[i].rarity);
					            		var unit_badge = "<div class='unit-badge'>";
					            		unit_badge += response.units[i].icon;
					            		unit_badge += "<div class='rarity'>";
					            		for (var j = 0; j < rarity; j++){
					            			unit_badge += "<img alt='gold star' src='<?php echo get_stylesheet_directory_uri(); ?>/images/star_gold_15.png'/>";
					            		}
					            		unit_badge += "</div>"
					            		unit_badge += "<h4>" + response.units[i].unit_number + " " + response.units[i].unit_name + "</h4>";
					            		unit_badge += "<select><option value='lord'>Lord</option><option value='anima'>Anima</option>";
					            		unit_badge += "<option value='breaker'>Breaker</option><option value='guardian'>Guardian</option>";
					            		unit_badge += "<option value='oracle'>Oracle</option></select>";
					            		unit_badge += "<button>Add Unit</button>"
					            		unit_badge += "</div>"
					            		$("#search_results").append(unit_badge);
					            	}
					            }
					            else {
					               alert("Unit(s) matching " + unit_name + " not found.")
					            }
					         }
					      })  
					});
				});
            </script>
        <?php
        else: ?>
        	<article>
        		<section>
        			<p>You must be logged in to use this feature.  Please login or create an account (add links to these).</p>
        		</section>
        	</article>
        <?php endif; ?>
		</section><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>