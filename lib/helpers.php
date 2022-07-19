<?php
	function get_open_govpub_nav_tab($title, $tab = false) {

		// Get the current tab
		$c_tab = (isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : false);

		// Create the link
		$link = add_query_arg( 
			array(
				'tab' 		=> $tab,
				'deleted_i' => false
			)
		);

		// Set the default css classes
		$classes = array( 'nav-tab'	);

		// Check if the tab needs to be active
		if( !$tab && !$c_tab || $tab == $c_tab )
			$classes[] = 'nav-tab-active';

		// Create the html
		$html = '<a href="' . $link . '" class="' . implode(' ', $classes) . '">';
		$html .= $title;
		$html .= '</a>';

		// Return the html
		return $html;

	}

	function the_open_govpub_nav_tab( $title, $tab = false ) {

		// Echo the results
		echo get_open_govpub_nav_tab( $title, $tab );

	}

	function get_open_govpub_source_organizations() {

		// Set the source url
		$source_url = 'https://standaarden.overheid.nl/owms/terms/Overheidsorganisatie.xml';

		// Set a result array
		$result = array();

		// Get the xml
		$xml = simplexml_load_file($source_url);

		// Check if a value returned
		if( isset($xml->value) && !empty($xml->value) ) {

			// Loop trough the values
			foreach ($xml->value as $value) {
				
				$result[] = $value->prefLabel->__toString();

			}

		}

		// Return the result
		return $result;

	}

	function get_open_govpub_setting( $name, $default = '' ) {

		 // Get settings
        $settings   = get_option( 'open_govpub_settings' );

        // Return setting
        return (isset($settings[$name]) ? $settings[$name] : $default);

	}

	function get_open_govpub_scheduled_time($cronname, $format = false) {

		if( !$format ) {
			
			// Get WordPress date and time format
			$date = get_option('date_format');
			$time = get_option('time_format');

			// Set format
			$format = sprintf(__('%s \a\t %s', 'open-govpub'), $date, $time);

		}

		$next_schedule = date('Y-m-d H:i:s', wp_next_scheduled($cronname));
		$next_schedule = get_date_from_gmt($next_schedule, 'Y-m-d H:i:s');

		if( $next_schedule )
			return date_i18n($format, strtotime($next_schedule));

		return __('not scheduled', 'open-govpub');

	}

	function get_open_govpub_last_import_string() {

		// Get last import date
		$import_date = get_open_govpub_option('last_import_date');

		if( $import_date )
			return date_i18n('j F Y H:i:s', strtotime($import_date));

		return __('never', 'open-govpub');

	}

	function get_open_govpub_current_import_string() {

		// Get total import data
		$total_import = get_open_govpub_option('total_import');

		// Check if total import isset
		if( $total_import && isset($total_import['status']) ) {

			// Return import status string
			return sprintf(
				__('%s of %s items imported', 'open-govpub'),
				$total_import['status'],
				$total_import['max_num']
			);

		}

		// Return default string
		return __('no import running', 'open-govpub');

	}

	function get_open_govpub_service_config( $service_id = false )
	{

		// Get the config array
		$config = include OPEN_GOVPUB_DIR . '/config/services.config.php';


		// Allow plugins to manipulate the config
		$config = apply_filters('open_govpub_service_config', $config, $service_id);

		// Check if single service needs to be returned
		if( $service_id && isset($config[$service_id]) ) {
			return $config[$service_id];
		}

		// Return the config
		return $config;

	}

	function get_open_govpub_option($option_name, $sub_item = false, $default = null)
	{

		// Get options
        $options   = get_option( 'open_govpub_options' );

        // Check if option exists
        if( $options && isset($options[$option_name]) ) {

        	// Set option
        	$option = $options[$option_name];

        	// If sub item needs to be retrieved
        	if( $sub_item ) {
        		
        		// Return sub item
        		return (isset($option[$sub_item]) ? $option[$sub_item] : $default);

        	} else {

        		// Return main option
        		return $option;

        	}

        }

        // Return default
        return $default;

	}

	function get_open_govpub_types_api_args()
	{
		
		return array(
			'hide_empty'   => array(
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'return'   => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

	}

	function get_open_govpub_search_api_args()
	{

		return array(
			's'   => array(
				'description'       => 'Limit results to those matching a string.',
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'open_govpub_type'   => array(
				'description'       => 'Find publications that are a member of this term (expects a slug)',
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'limit'   => array(
				'type'              => 'integer',
				'default'           => 20,
				'sanitize_callback' => 'absint',
			),
			'page'   => array(
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'orderby'   => array(
				'type'              => 'string',
				'default'           => 'date',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'order'   => array(
				'type'              => 'string',
				'default'           => 'DESC',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'fields'   => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);

	}

	function update_open_govpub_sub_option($option_name, $subname, $value)
	{

		// Get options
		$options   = get_option( 'open_govpub_options' );

		// If options doesn't exists or is not an array
		if( !isset($options[$option_name]) || !is_array($options[$option_name]) ) {

			// Make array
			$options[$option_name] = array();

		}

		// Set the new value
		$options[$option_name][$subname] = $value;

		// Save the options
		return update_option('open_govpub_options', $options);

	}

	function update_open_govpub_option($option_name, $value)
	{

		// Get options
		$options   = get_option( 'open_govpub_options' );

		// Set the new value
		$options[$option_name] = $value;

		// Save the options
		return update_option('open_govpub_options', $options);

	}

	function is_govpub_import_locked() {

		// Get the transient
		return get_transient('govpub_import_locked');

	}

	function is_govpub_import_check_locked() {

		// Get the transient
		return get_transient('govpub_import_check_locked');

	}

	function delete_open_govpub_option($option_name)
	{

		// Get options
        $options   = get_option( 'open_govpub_options' );

        // Check if option exists and unset if needed
        if( isset($options[$option_name]) )
        	unset($options[$option_name]);

        // Save the options
        return update_option('open_govpub_options', $options);

	}