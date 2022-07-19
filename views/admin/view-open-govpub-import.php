<div class="wrap">
	<h1><?php echo get_admin_page_title(); ?></h1>
	<table class="form-table">
		<tr>
			<th><?php _e('Last import', 'open-govpub'); ?></th>
			<td><?php echo get_open_govpub_last_import_string(); ?></td>
		</tr>
		<tr>
			<th><?php _e('Next import (check)', 'open-govpub'); ?></th>
			<td><?php echo get_open_govpub_scheduled_time('open_govpub_check_import_publications'); ?></td>
		</tr>
		<tr>
			<th><?php _e('Next import (task)', 'open-govpub'); ?></th>
			<td><?php echo get_open_govpub_scheduled_time('open_govpub_task_import_publications'); ?></td>
		</tr>
		<tr>
			<th><?php _e('Current import', 'open-govpub'); ?></th>
			<td id="open_govpub--import-string"><?php echo get_open_govpub_current_import_string(); ?></td>
		</tr>
		<tr>
			<th></th>
			<td>
				<button type="button" id="open_govpub--start-import" class="button button-primary"><?php _e('Run manual import', 'open-govpub'); ?></button>
				<button type="button" id="open_govpub--halt-import" class="button"><?php _e('Abort manual import', 'open-govpub'); ?></button>
			</td>
		</tr>
	</table>
	<div class="open_govpub--import-wrap">
		<div class="open_govpub--import-bar">
			<div class="open_govpub--import-progress"><span></span></div>
		</div>
	</div>
</div>