<?php

namespace Pronamic\WordPress\Twinfield\Plugin;

class Settings {
	/**
	 * Twinfield plugin object.
	 *
	 * @var Plugin
	 */
	private $plugin;

		/**
		 * Constructs and initialize Twinfield plugin settings.
		 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;

		
	}

	public function setup() {
		\add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Admin initialize
	 */
	public function admin_init() {
		// Section - OpenID Connect Authentication.
		add_settings_section(
			'pronamic_twinfield_openid_connect_authentication',
			__( 'OpenID Connect Authentication', 'twinfield' ),
			array( $this, 'section_openid_connect_authentication' ),
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
			array(
				'label_for' => 'pronamic_twinfield_openid_connect_client_id',
			)
		);

		// Client Secret.
		register_setting( 'pronamic_twinfield', 'pronamic_twinfield_openid_connect_client_secret' );

		add_settings_field(
			'pronamic_twinfield_openid_connect_client_secret',
			__( 'Client Secret', 'twinfield' ),
			__NAMESPACE__ . '\SettingFields::render_text',
			'pronamic_twinfield',
			'pronamic_twinfield_openid_connect_authentication',
			array(
				'label_for' => 'pronamic_twinfield_openid_connect_client_secret',
				'type'      => 'password',
			)
		);

		// Redirect URI.
		register_setting( 'pronamic_twinfield', 'pronamic_twinfield_openid_connect_redirect_uri' );

		add_settings_field(
			'pronamic_twinfield_openid_connect_redirect_uri',
			__( 'Redirect URI', 'twinfield' ),
			__NAMESPACE__ . '\SettingFields::render_text',
			'pronamic_twinfield',
			'pronamic_twinfield_openid_connect_authentication',
			array(
				'label_for' => 'pronamic_twinfield_openid_connect_redirect_uri',
				'type'      => 'url',
			)
		);

		// Connect Link.
		if ( $this->plugin->openid_connect_client ) {
			add_settings_field(
				'pronamic_twinfield_openid_connect_link',
				__( 'Connection', 'twinfield' ),
				array( $this, 'field_connect_link' ),
				'pronamic_twinfield',
				'pronamic_twinfield_openid_connect_authentication'
			);
		}
	}

	/**
	 * Section.
	 */
	public function field_connect_link() {
		if ( empty( $this->plugin->openid_connect_client ) ) {
			return;
		}

		$openid_connect_client = $this->plugin->openid_connect_client;

		$access_token = $this->plugin->get_access_token();

		$label = __( 'Connect with Twinfield', 'twinfield' );

		if ( $access_token ) {
			$label = __( 'Reconnect with Twinfield', 'twinfield' );
		}

		$state               = new \stdClass();
		$state->redirect_uri = add_query_arg( 'page', 'twinfield_settings', admin_url( 'admin.php' ) );

		$url = $openid_connect_client->get_authorize_url( $state );

		printf(
			'<a href="%s">%s</a>',
			esc_url( $url ),
			esc_html( $label )
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
				esc_url( 'https://www.twinfield.nl/openid-connect-request/' ),
				esc_html__( 'Twinfield web site', 'twinfield' )
			)
		);
	}
}
