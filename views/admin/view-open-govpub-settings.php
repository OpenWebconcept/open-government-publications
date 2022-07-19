<div class="wrap">
	<h1><?php echo get_admin_page_title(); ?></h1>
	<hr class="wp-header-end">
	<?php
		// Include the navigation tabs
		include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-tabs.php';
	?>
	<form method="post" action="options.php">
		<?php
			settings_fields( 'open_govpub' );
			do_settings_sections( 'open_govpub' );
			submit_button();
		?>
	</form>
</div>