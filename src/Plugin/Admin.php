<?php

namespace Pronamic\WordPress\Twinfield\Plugin;

class Admin {
	/**
	 * Plugin.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Construct admin.
	 *
	 * @param Plugin $pluin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->settings = new Settings( $plugin );
	}

	/**
	 * Setup.
	 */
	public function setup() {
		\add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		$this->settings->setup();
	}

	/**
	 * Admin menu.
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Twinfield', 'twinfield' ),
			__( 'Twinfield', 'twinfield' ),
			'manage_options',
			'pronamic-twinfield',
			array( $this, 'page_dashboard' ),
			'dashicons-admin-site-alt3'
		);

		add_submenu_page(
			'pronamic-twinfield',
			_x( 'Twinfield Offices', 'twinfield.com', 'twinfield' ),
			_x( 'Offices', 'twinfield.com', 'twinfield' ),
			'manage_options',
			'twinfield_offices',
			array( $this, 'page_offices' )
		);

		add_submenu_page(
			'pronamic-twinfield',
			__( 'Twinfield Settings', 'twinfield' ),
			__( 'Settings', 'twinfield' ),
			'manage_options',
			'pronamic_twinfield_settings',
			function() {
				include __DIR__ . '/../../admin/page-settings.php';
			}
		);
	}
}
