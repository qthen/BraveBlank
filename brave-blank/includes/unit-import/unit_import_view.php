<?php
function upload_form() {
	$log = array();
	// form HTML {{{
?>

<div class="wrap">
	<h2>Import Unit Data CSV</h2>
	
	<?php 
	if (!empty($log)) :
		// messages HTML {{{
	?>

		<div class="wrap">
			<?php if (!empty($log['error'])): ?>
		
			<div class="error">
		
				<?php foreach ($log['error'] as $error): ?>
					<p><?php echo $error; ?></p>
				<?php endforeach; ?>
		
			</div>
		
			<?php endif; ?>
		
			<?php if (!empty($log['notice'])): ?>
		
			<div class="updated fade">
		
				<?php foreach ($log['notice'] as $notice): ?>
					<p><?php echo $notice; ?></p>
				<?php endforeach; ?>
		
			</div>
		
			<?php endif; ?>
		</div><!-- end wrap -->

	<?php
		// end messages HTML }}}
		$log = array();
	endif;
	?>
	
	<form action="admin-post.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="brave_blank_unit_import" />
		<?php wp_nonce_field( 'brave_blank_unit_import_verify' ); ?>

		<!-- Import as draft -->
		<p>
		<input name="_unit_importer_import_as_draft" type="hidden" value="publish" />
		<label><input name="unit_importer_import_as_draft" type="checkbox" value="draft" /> Import units as drafts</label>
		</p>
		
		<!-- File input -->
		<p><label for="unit_import">Upload file:</label><br/>
			<input name="unit_import" id="unit_import" type="file" value="" aria-required="true" /></p>
		<p class="submit"><input type="submit" class="button" name="submit" value="Import" /></p>
	</form>
</div><!-- end wrap -->

<?php
		// end form HTML }}}
}