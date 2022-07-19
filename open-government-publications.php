<?php
/**
 * Plugin Name: Open Government Publications
 * Description: With this plugin you can make Government Publications available trought a REST API
 * Version: 1.0.0
 * Author: Súdwest-Fryslân
 * Author URI: https://sudwestfryslan.nl/
 * Requires at least: 4.8
 * Tested up to: 5.2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! defined('OPEN_GOVPUB_VERSION')) define('OPEN_GOVPUB_VERSION', '1.0.0');
if ( ! defined('OPEN_GOVPUB_DEBUG')) define('OPEN_GOVPUB_DEBUG', false);

if ( ! defined('OPEN_GOVPUB_FILE')) define('OPEN_GOVPUB_FILE', __FILE__);
if ( ! defined('OPEN_GOVPUB_DIR')) define('OPEN_GOVPUB_DIR', dirname(__FILE__));
if ( ! defined('OPEN_GOVPUB_BASENAME')) define('OPEN_GOVPUB_BASENAME', basename(OPEN_GOVPUB_DIR));
if ( ! defined('OPEN_GOVPUB_URL')) define('OPEN_GOVPUB_URL', plugins_url(OPEN_GOVPUB_BASENAME));

/**
 * Main Plugin Class
 *
 * @class openGovpub
 * @version  1.1.0
 */
class openGovpub {

	public function __construct() {

		// Load the language
		load_plugin_textdomain(
			'open-govpub',
			false,
			OPEN_GOVPUB_BASENAME . '/languages'
		);

		// Include the helper functions
		include OPEN_GOVPUB_DIR . '/lib/helpers.php';

		// Include the init class
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-init.php';

		// Include the cronjob class
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-cronjobs.php';

		// Include the admin settings class
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-settings.php';

		// Include the admin menu class
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-admin-menu.php';

		// Include the post type class
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-post-type.php';

		// Include the meta boxes class
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-meta-boxes.php';

		// Include the service
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-service.php';

		// Include the import class
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-import.php';

		// Include the REST-api class
		include OPEN_GOVPUB_DIR . '/classes/class-open-govpub-api.php';

	}

}
$GLOBALS['open-govpub'] = new openGovpub();