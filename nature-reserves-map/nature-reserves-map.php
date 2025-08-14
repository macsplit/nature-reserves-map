<?php
/**
 * Plugin Name: Nature Reserves Map
 * Plugin URI: https://example.com/nature-reserves-map
 * Description: Manage and display nature reserves on an interactive map
 * Version: 1.0.3
 * Author: Lee Hanken
 * License: GPL v2 or later
 * Text Domain: nature-reserves-map
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('NRM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NRM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('NRM_PLUGIN_VERSION', '1.0.3');

// Include required files
require_once NRM_PLUGIN_DIR . 'includes/class-database.php';
require_once NRM_PLUGIN_DIR . 'includes/class-admin.php';
require_once NRM_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once NRM_PLUGIN_DIR . 'includes/class-api.php';

// Activation hook
register_activation_hook(__FILE__, ['NRM_Database', 'create_table']);

// Deactivation hook
register_deactivation_hook(__FILE__, ['NRM_Database', 'deactivate']);

// Initialize plugin
add_action('init', 'nrm_init');
function nrm_init() {
    // Initialize admin if in admin area
    if (is_admin()) {
        new NRM_Admin();
    }
    
    // Initialize shortcode
    new NRM_Shortcode();
    
    // Initialize API
    new NRM_API();
}
