<?php
/**
 * Pronamic Twinfield
 *
 * @package           Pronamic\WordPress\Twinfield\Plugin
 * @author            Pronamic
 * @copyright         2021 Pronamic
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Pronamic Twinfield
 * Plugin URI:        https://www.pronamic.eu/plugins/twinfield/
 * Description:       Twinfield plugin for WordPress.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Pronamic
 * Author URI:        https://www.pronamic.eu/
 * Text Domain:       pronamic-twinfield
 * Domain Path:       /languages/
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * Autoload.
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Action Scheduler.
 */
require __DIR__ . '/vendor/woocommerce/action-scheduler/action-scheduler.php';

/**
 * Bootstrap.
 */
add_action(
	'init',
	function () {
		load_plugin_textdomain( 'pronamic-pay-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

$pronamic_twinfield_plugin = Pronamic\WordPress\Twinfield\Plugin\Plugin::instance();

$pronamic_twinfield_plugin->setup();
