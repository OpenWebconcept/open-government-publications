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

if (! defined('ABSPATH')) {
    exit();
}

require __DIR__ . '/vendor/autoload.php';

$plugin = new \SudwestFryslan\OpenGovernmentPublications\Plugin();
$plugin->boot();
