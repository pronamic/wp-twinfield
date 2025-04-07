<?php
/**
 * Settings
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

/**
 * Settings
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Settings {
	/**
	 * Twinfield plugin object.
	 *
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Construct Twinfield plugin settings.
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
		\add_action( 'admin_init', $this->admin_init( ... ) );
	}

	/**
	 * Admin initialize.
	 *
	 * @return void
	 */
	public function admin_init() {
		// Section - OpenID Connect Authentication.
		add_settings_section(
			'pronamic_twinfield_openid_connect_authentication',
			__( 'OpenID Connect Authentication', 'twinfield' ),
			$this->section_openid_connect_authentication( ... ),
			'pronamic_twinfield'
		);

		// Client ID.
		register_setting( 'pronamic_twinfield', 'pronamic_twinfield_openid_connect_client_id' );

		add_settings_field(
			'pronamic_twinfield_openid_connect_client_id',
			__( 'Client ID', 'twinfield' ),
			__NAMESPACE__ . '\SettingFields::render_text',
			'pronamic_twinfield',
			'pronamic_twinfield_openid_connect_authentication',
			[
				'label_for' => 'pronamic_twinfield_openid_connect_client_id',
			]
		);

		// Client Secret.
		register_setting( 'pronamic_twinfield', 'pronamic_twinfield_openid_connect_client_secret' );

		add_settings_field(
			'pronamic_twinfield_openid_connect_client_secret',
			__( 'Client Secret', 'twinfield' ),
			__NAMESPACE__ . '\SettingFields::render_text',
			'pronamic_twinfield',
			'pronamic_twinfield_openid_connect_authentication',
			[
				'label_for' => 'pronamic_twinfield_openid_connect_client_secret',
				'type'      => 'password',
			]
		);

		// Section - General.
		add_settings_section(
			'pronamic_twinfield_general',
			__( 'General', 'twinfield' ),
			function (): void { },
			'pronamic_twinfield'
		);

		// Default Config.
		register_setting( 'pronamic_twinfield', 'pronamic_twinfield_authorization_post_id' );

		add_settings_field(
			'pronamic_twinfield_authorization_post_id',
			__( 'Default Authorization', 'pronamic-twinfield' ),
			$this->input_page( ... ),
			'pronamic_twinfield',
			'pronamic_twinfield_general',
			[
				'post_type'        => 'pronamic_twf_auth',
				'show_option_none' => __( '— Select Authorization —', 'pronamic-twinfield' ),
				'label_for'        => 'pronamic_twinfield_authorization_post_id',
			]
		);
	}

	/**
	 * Input page.
	 *
	 * @param array $args Arguments.
	 * @return void
	 */
	public function input_page( $args ) {
		$name = $args['label_for'];

		$selected = get_option( $name, '' );

		if ( false === $selected ) {
			$selected = '';
		}

		wp_dropdown_pages(
			[
				'name'             => esc_attr( $name ),
				'post_type'        => esc_attr( $args['post_type'] ?? 'page' ),
				'selected'         => esc_attr( $selected ),
				'show_option_none' => esc_attr( $args['show_option_none'] ?? __( '— Select a page —', 'pronamic-twinfield' ) ),
				'class'            => 'regular-text',
			]
		);
	}

	/**
	 * Section.
	 */
	public function section_openid_connect_authentication() {
		printf(
			'Go to the %s in order to register your OpenID Connect / OAuht 2.0 client and get your Client Id (and optional client secret).',
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://developers.twinfield.com/clients/new' ),
				esc_html__( 'Twinfield web site', 'twinfield' )
			)
		);
	}
}
