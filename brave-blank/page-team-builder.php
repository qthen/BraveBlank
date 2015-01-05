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
            $unit_search_nonce = wp_create_nonce("unit_search");
            $add_unit_nonce = wp_create_nonce("add_unit");
            $remove_unit_nonce = wp_create_nonce("remove_unit");
    		$searchLink = admin_url('admin-ajax.php?action=unit_search&nonce='.$unit_search_nonce);
    		$addUnitLink = admin_url('admin-ajax.php?action=add_unit&nonce='.$add_unit_nonce);
    		$removeUnitLink = admin_url('admin-ajax.php?action=remove_unit&nonce='.$remove_unit_nonce);
    		?>
    		<h3>My Unit Roster</h3>
    		<div id="user_units">
			<?php
				//delete_user_option( get_current_user_id(), 'brave_unit_roster' );
				$users_units = get_user_option('brave_unit_roster');
				//var_dump($users_units);
				if ($users_units != false):
    				foreach ($users_units as $unit){
    					//var_dump($unit);
    					$unit_badge = "<div class='unit-badge'>";
	            		$unit_badge .= $unit['icon'];
    					$unit_badge .= "<div class='rarity'>";
	            		for ($j = 0; $j < (int)$unit['rarity']; $j++){
	            			$unit_badge .= "<img alt='gold star' src=' " . get_stylesheet_directory_uri() . "/images/star_gold_15.png'/>";
	            		}
	            		$unit_badge .= "</div>";
	            		$unit_badge .= "<h4>" . $unit['unit_number'] . " " . $unit['unit_name'] . "</h4>";
	        			$unit_badge .= "<p>Type: " . ucwords($unit['unit_type']) . "<br/>";
	        			$unit_badge .= "HP: " . $unit['hp'] . "<br/>";
	        			$unit_badge .= "Attack: " . $unit['attack'] . "<br/>";
	        			$unit_badge .= "Defense: " . $unit['defense'] . "<br/>";
	        			$unit_badge .= "Recovery: " . $unit['recovery'] . "</p>";
	        			$unit_badge .= "<button data-unitid='" . $unit['unit_number'] . "' class='remove-unit' >Remove Unit</button>";
	            		$unit_badge .= "</div>";
	            		echo $unit_badge;
    				}
    			else:
			?>
    			<p id="no-units">You have no units</p>
    		<?php endif; ?>
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
					         url : "<?php echo $searchLink; ?>&q=" + unit_name,
					         success: function(response) {
					            if(response.type == "success") {
					            	$("#search_results").html('');
					            	for (var i = 0; i < response.units.length; i++){
					            		var html = unit_badge(response.units[i], "search_result");
					            		$("#search_results").append(html);
					            	}
					            	add_unit_button();
					            }
					            else if (response.type == "error") {
					            	alert(response.message);
					            }else{
					               	alert("There was an unknown error.");
					            }
					        }
						})  
					});
					$('.remove-unit').click(function (){
						var unit_id = $(this).data('unitid');
						var unit_element = $(this).parent();
						$.ajax({
							type: "GET",
							dataType: "json",
							url : "<?php echo $removeUnitLink; ?>&unit-id=" + unit_id,
							success: function(response){
								if(response.type == "success"){
									unit_element.remove();
									alert(response.message);
								}else if(response.type == "error"){
									alert(response.message);
								}else{
									alert("There was an unknown error.");
								}
							}
						});
					});
					function add_unit_button(){
						$('.add-unit').click(function (){
							var unit_id = $(this).data('unitid');
							var unit_type = $(this).siblings('select').val();
							$.ajax({
								type: "GET",
								dataType: "json",
								url : "<?php echo $addUnitLink; ?>&unit-id=" + unit_id + "&unit-type=" + unit_type,
								success: function(response){
									if(response.type == "success"){
										for (var i = 0; i < response.units.length; i++){
											var html = unit_badge(response.units[i], "regular_unit");
					            			$("#user_units").append(html);
										}
									}else if(response.type == "error"){
										alert(response.message);
									}else{
										alert("There was an unknown error.");
									}
								}
							});
						});
					}
					function unit_badge(unit_data, type){
						var rarity = parseInt(unit_data.rarity);
	            		var unit_badge = "<div class='unit-badge'>";
	            		unit_badge += unit_data.icon;
	            		unit_badge += "<div class='rarity'>";
	            		for (var j = 0; j < rarity; j++){
	            			unit_badge += "<img alt='gold star' src='<?php echo get_stylesheet_directory_uri(); ?>/images/star_gold_15.png'/>";
	            		}
	            		unit_badge += "</div>"
	            		unit_badge += "<h4>" + unit_data.unit_number + " " + unit_data.unit_name + "</h4>";
	            		if (type == "search_result"){
	            			unit_badge += "<select><option value='lord'>Lord</option><option value='anima'>Anima</option>";
	            			unit_badge += "<option value='breaker'>Breaker</option><option value='guardian'>Guardian</option>";
	            			unit_badge += "<option value='oracle'>Oracle</option></select>";
	            			unit_badge += "<button data-unitid='" + unit_data.unit_number + "' class='add-unit' >Add Unit</button>"
	            		}
	            		else{
	            			unit_badge += "<p>Type: " + unit_data.unit_type + "<br/>";
	            			unit_badge += "HP: " + unit_data.hp + "<br/>";
	            			unit_badge += "Attack: " + unit_data.attack + "<br/>";
	            			unit_badge += "Defense: " + unit_data.defense + "<br/>";
	            			unit_badge += "Recovery: " + unit_data.recovery + "</p>";
	            		}
	            		
	            		unit_badge += "</div>"
						return unit_badge;
					}
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