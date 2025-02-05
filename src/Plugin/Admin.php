<?php
/**
 * Admin
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

/**
 * Plugin
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Admin {
	/**
	 * Plugin.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Settings.
	 *
	 * @var Settings
	 */
	public $settings;

	/**
	 * Construct admin.
	 *
	 * @param Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->settings = new Settings( $plugin );
	}

	/**
	 * Setup.
	 */
	public function setup() {
		\add_action( 'admin_menu', [ $this, 'admin_menu' ] );

		$this->settings->setup();
	}

	/**
	 * Get menu icon URL.
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 * @return string
	 * @throws \Exception Throws exception when retrieving menu icon fails.
	 */
	private function get_menu_icon_url() {
		/**
		 * Icon URL.
		 *
		 * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
		 * This should begin with 'data:image/svg+xml;base64,'.
		 *
		 * We use a SVG image with default fill color #A0A5AA from the default admin color scheme:
		 * https://github.com/WordPress/WordPress/blob/5.2/wp-includes/general-template.php#L4135-L4145
		 *
		 * The advantage of this is that users with the default admin color scheme do not see the repaint:
		 * https://github.com/WordPress/WordPress/blob/5.2/wp-admin/js/svg-painter.js
		 *
		 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
		 */
		$file = __DIR__ . '/../../images/dist/wp-twinfield-wp-admin-fresh-base.svgo-min.svg';

		if ( ! \is_readable( $file ) ) {
			throw new \Exception(
				\sprintf(
					'Could not read WordPress admin menu icon from file: %s.',
					\esc_html( $file )
				)
			);
		}

		$svg = \file_get_contents( $file, true );

		if ( false === $svg ) {
			throw new \Exception(
				\sprintf(
					'Could not read WordPress admin menu icon from file: %s.',
					\esc_html( $file )
				)
			);
		}

		$icon_url = \sprintf(
			'data:image/svg+xml;base64,%s',
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			\base64_encode( $svg )
		);

		return $icon_url;
	}

	/**
	 * Admin menu.
	 */
	public function admin_menu() {
		try {
			$menu_icon_url = $this->get_menu_icon_url();
		} catch ( \Exception $e ) {
			/**
			 * If retrieving the menu icon URL fails we will
			 * fallback to the WordPress site dashicon.
			 *
			 * @link https://developer.wordpress.org/resource/dashicons/#admin-site-alt3
			 */
			$menu_icon_url = 'dashicons-admin-site-alt3';
		}

		\add_menu_page(
			\__( 'Twinfield', 'pronamic-twinfield' ),
			\__( 'Twinfield', 'pronamic-twinfield' ),
			'manage_options',
			'pronamic-twinfield',
			function () {
				include __DIR__ . '/../../admin/page-dashboard.php';
			},
			$menu_icon_url
		);

		\add_submenu_page(
			'pronamic-twinfield',
			\__( 'Twinfield Authorizations', 'pronamic-twinfield' ),
			\__( 'Authorizations', 'pronamic-twinfield' ),
			'manage_options',
			\add_query_arg( 'post_type', 'pronamic_twf_auth', 'edit.php' ),
			'',
			10
		);

		add_submenu_page(
			'pronamic-twinfield',
			\__( 'Twinfield Settings', 'twinfield' ),
			\__( 'Settings', 'twinfield' ),
			'manage_options',
			'pronamic-twinfield-settings',
			function () {
				include __DIR__ . '/../../admin/page-settings.php';
			},
			20
		);
	}
}
