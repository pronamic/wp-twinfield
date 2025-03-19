<?php
/**
 * Save hierarchy controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_Query;
use WP_REST_Request;

/**
 * Save hierarchy controller class
 */
class SaveHierarchyController {
	/**
	 * Plugin.
	 * 
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Construct REST controller.
	 * 
	 * @param Plugin $plugin Plugin.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_action( 'pronamic_twinfield_save_hierarchies', $this->save_hierarchies( ... ) );
	}

	/**
	 * Save hierarchies.
	 * 
	 * @param string|int $authorization Authorization.
	 * @return void
	 */
	private function save_hierarchies( $authorization ) {
		
	}
}
