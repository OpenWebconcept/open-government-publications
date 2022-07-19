<nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondair menu">
	<?php
		// Create the tabs
		the_open_govpub_nav_tab(__('Settings', 'open-govpub'), false);
		the_open_govpub_nav_tab(__('Reset', 'open-govpub'), 'reset');
		the_open_govpub_nav_tab(__('Endpoints', 'open-govpub'), 'endpoints');
	?>
</nav>