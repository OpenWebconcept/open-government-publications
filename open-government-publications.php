<?php

/**
 * Plugin Name: Open Government Publications
 * Description: With this plugin you can make Government Publications available trought a REST API
 * Version: 1.0.0
 * Author: Súdwest-Fryslân
 * Author URI: https://sudwestfryslan.nl/
 * Requires at least: 5.1
 * Tested up to: 6.1
 */

if (! defined('ABSPATH')) exit; // Exit if accessed directly

if (! defined('OPEN_GOVPUB_VERSION')) define('OPEN_GOVPUB_VERSION', '1.0.0');
if (! defined('OPEN_GOVPUB_DEBUG')) define('OPEN_GOVPUB_DEBUG', false);

if (! defined('OPEN_GOVPUB_FILE')) define('OPEN_GOVPUB_FILE', __FILE__);
if (! defined('OPEN_GOVPUB_DIR')) define('OPEN_GOVPUB_DIR', dirname(__FILE__));
if (! defined('OPEN_GOVPUB_BASENAME')) define('OPEN_GOVPUB_BASENAME', basename(OPEN_GOVPUB_DIR));
if (! defined('OPEN_GOVPUB_URL')) define('OPEN_GOVPUB_URL', plugins_url(OPEN_GOVPUB_BASENAME));

require __DIR__ . '/vendor/autoload.php';

$plugin = new \SudwestFryslan\OpenGovernmentPublications\Plugin();
$plugin->boot();

// class openGovpub
// {

//     public function __construct()
//     {
//         load_plugin_textdomain(
//             'open-govpub',
//             false,
//             OPEN_GOVPUB_BASENAME . '/languages'
//         );
//     }
// }

// $GLOBALS['open-govpub'] = new openGovpub();
