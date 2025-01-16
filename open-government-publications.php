<?php

/**
 * Plugin Name: Open Government Publications
 * Description: Import and publish Government Publications through the REST API.
 * Version: 2.0.9
 * License: EUPL-1.2
 * Author: Súdwest-Fryslân
 * Author URI: https://sudwestfryslan.nl/
 * Requires at least: 5.1
 * Tested up to: 6.7
 * Requires PHP: 8.1
 */

if (! defined('ABSPATH')) {
    exit();
}

require __DIR__ . '/vendor/autoload.php';

$plugin = new \SudwestFryslan\OpenGovernmentPublications\Plugin();
$plugin->boot();
