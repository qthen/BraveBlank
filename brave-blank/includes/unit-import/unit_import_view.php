<?php
function upload_form() {
	if ( isset( $_GET['message'] ))
  {
?>
   <div id='message' class='updated fade'><p><strong>$_GET['message'] )</strong></p></div>
<?php
  }
?>
	// form HTML {{{
	?>
	
	<form action="admin-post.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="brave_blank_unit_import" />
		<?php wp_nonce_field( 'brave_blank_unit_import_verify' ); ?>
		
		<!-- File input -->
		<p><label for="unit_import">Upload file:</label><br/>
			<input name="unit_import" id="unit_import" type="file" value="" aria-required="true" /></p>
		<p class="submit"><input type="submit" class="button" name="submit" value="Import" /></p>
	</form>
</div><!-- end wrap -->

<?php
		// end form HTML }}}
}