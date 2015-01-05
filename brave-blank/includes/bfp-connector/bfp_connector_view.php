<?php
function bfp_connector_form() {
	$message = "";
	if( isset($_GET['message'])){
		$message = $_GET['message'];
	}
	// form HTML {{{
?>
<div class="wrap">
	<h2>Fetch Data from Brave Frontier PROs</h2>
	
	<?php 
		// messages HTML {{{
	?>
		<div class="wrap">		
			<?php if (trim((string)$message) != ""): ?>
			<div class="updated fade"><p><?php echo $message; ?></p></div>
			<?php endif; ?>
		</div><!-- end wrap -->
	<p><?php echo $_SERVER['SERVER_ADDR'];?></p>
	<form action="admin-post.php" method="post" >
		<input type="hidden" name="action" value="bfp_connector" />
		<?php wp_nonce_field( 'bfp_connector' ); ?>
		
		<!-- File input -->
		<p><label for="unit_number">Unit Number:</label><br/>
			<input name="unit_number" id="unit_id" type="text" value="" aria-required="true" /></p>
		<p class="submit"><input type="submit" class="button" name="submit" value="Import" /></p>
	</form>
</div><!-- end wrap -->
<?php
		// end form HTML }}}
}