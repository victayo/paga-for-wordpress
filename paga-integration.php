<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/victayo
 * @since             1.0.0
 * @package           Paga_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       Paga Integration
 * Plugin URI:        https://github.com/victayo/paga-for-wordpress
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Victor Temitayo Okala
 * Author URI:        https://github.com/victayo
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       paga-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PAGA_INTEGRATION_VERSION', '1.1.1' );

define('API_TOKEN', 'jGStI2ne0HEol4Xw');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-paga-integration-activator.php
 */
function activate_paga_integration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-paga-integration-activator.php';
	Paga_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-paga-integration-deactivator.php
 */
function deactivate_paga_integration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-paga-integration-deactivator.php';
	Paga_Integration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_paga_integration' );
register_deactivation_hook( __FILE__, 'deactivate_paga_integration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-paga-integration.php';

/**
 * @todo Enable woocommerce integration
 */
// require_once plugin_dir_path( __FILE__ ) . 'paga-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_paga_integration() {

	$plugin = new Paga_Integration();
	$plugin->run();

}
run_paga_integration();
