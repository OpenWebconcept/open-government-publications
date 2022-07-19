<div class="wrap">
	<h1><?php _e('API Endpoints', 'open-govpub'); ?></h1>
	<hr class="wp-header-end">
	<?php
		// Include the navigation tabs
		include OPEN_GOVPUB_DIR . '/views/admin/view-open-govpub-tabs.php';
	?>
	
	<h2><?php _e('Types endpoint', 'open-govpub'); ?></h2>
	<code><?php echo site_url('/wp-json/owc/govpub/v1/types'); ?></code>
	
	<h3><?php _e('URL parameters', 'open-govpub'); ?></h3>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td><?php _e('Parameter', 'open-govpub'); ?></td>
				<td><?php _e('Description', 'open-govpub'); ?></td>
				<td><?php _e('Type', 'open-govpub'); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($types_args as $param => $info) { ?>
				<tr>
					<th><strong><?php echo $param; ?></strong></th>
					<td><?php echo $info['description']; ?></td>
					<td><?php echo $info['type']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<br><hr>

	<h2><?php _e('Search endpoint', 'open-govpub'); ?></h2>
	<code><?php echo site_url('/wp-json/owc/govpub/v1/search'); ?></code>
	
	<h3><?php _e('URL parameters', 'open-govpub'); ?></h3>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td><?php _e('Parameter', 'open-govpub'); ?></td>
				<td><?php _e('Description', 'open-govpub'); ?></td>
				<td><?php _e('Type', 'open-govpub'); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($search_args as $param => $info) { ?>
				<tr>
					<th><strong><?php echo $param; ?></strong></th>
					<td><?php echo $info['description']; ?></td>
					<td><?php echo $info['type']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	

</div>