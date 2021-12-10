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
		\add_menu_page(
			\__( 'Twinfield', 'pronamic-twinfield' ),
			\__( 'Twinfield', 'pronamic-twinfield' ),
			'manage_options',
			'pronamic-twinfield',
			array( $this, 'page_dashboard' ),
			'dashicons-admin-site-alt3'
		);

		\add_submenu_page(
			'pronamic-twinfield',
			\__( 'Twinfield Authorizations', 'pronamic-twinfield' ),
			\__( 'Authorizations', 'pronamic-twinfield' ),
			'manage_options',
			\add_query_arg( 'post_type', 'twinfield_auth', 'edit.php' ),
			'',
			10
		);

		\add_submenu_page(
			'pronamic-twinfield',
			\__( 'Twinfield Offices', 'pronamic-twinfield' ),
			\__( 'Offices', 'pronamic-twinfield' ),
			'manage_options',
			'pronamic-twinfield-offices',
			array( $this, 'page_offices' ),
			20
		);

		add_submenu_page(
			'pronamic-twinfield',
			\__( 'Twinfield Settings', 'twinfield' ),
			\__( 'Settings', 'twinfield' ),
			'manage_options',
			'pronamic-twinfield-settings',
			function() {
				include __DIR__ . '/../../admin/page-settings.php';
			},
			30
		);
	}
}
