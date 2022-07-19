<div class="wrap">
	<h1><?php _e('Reset Open Government Publications', 'open-govpub'); ?></h1>
	<hr class="wp-header-end">
	<?php
		// Include the navigation tabs
		include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-tabs.php';
	?>
	<form method="post" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">
		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e('Which data needs resetting?', 'open-govpub'); ?></th>
					<td>
						<select name="reset">
							<option value="statistics" <?php selected($c_reset, 'statistics', true); ?>>
								<?php _e('Import statistics', 'open-govpub'); ?>
							</option>
							<option value="posts" <?php selected($c_reset, 'posts', true); ?>>
								<?php _e('Posts and statistics', 'open-govpub'); ?>
							</option>
							<option value="all" <?php selected($c_reset, 'all', true); ?>>
								<?php _e('Reset all', 'open-govpub'); ?>
							</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="action" value="reset_open_govpub">
		<input type="hidden" name="referer" value="<?php echo add_query_arg(array('tab' => 'reset')); ?>">
		<button type="submit" class="button button-primary"><?php _e('Reset', 'open-govpub'); ?></button>
	</form>

	<br><br><br><br>

	<h2><?php _e('Reset description', 'open-govpub'); ?></h2>
	
	<strong>- <?php _e('Import statistics', 'open-govpub'); ?></strong>
	<p><?php _e('Delete all import statistic what will trigger a re-update of all import data but doesn\'t delete the posts', 'open-govpub'); ?></p>
	<br>

	<strong>- <?php _e('Posts and statistics', 'open-govpub'); ?></strong>
	<p><?php _e('Delete all posts and import statistic what will trigger a re-import but keep the settings', 'open-govpub'); ?></p>
	<br>

	<strong>- <?php _e('Reset all', 'open-govpub'); ?></strong>
	<p><?php _e('Delete all the settings, statistics and posts', 'open-govpub'); ?></p>
	<br>

</div>