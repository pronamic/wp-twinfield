<?php
/**
 * Plugin
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;

/**
 * Plugin
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Plugin {
    public function __construct( $file ) {
        $this->file = $file;

        // REST API
        $this->rest_api = new RestApi( $this );

        // Admin.
        if ( is_admin() ) {
            $this->admin = new Admin( $this );
        }
    }

    public function setup() {
        $this->setup_openid_connect_client();

        $this->rest_api->setup();

        if ( null !== $this->admin ) {
            $this->admin->setup();
        }
    }

    private function setup_openid_connect_client() {
        $client_id     = \get_option( 'pronamic_twinfield_openid_connect_client_id' );
        $client_secret = \get_option( 'pronamic_twinfield_openid_connect_client_secret' );
        $redirect_uri  = \get_option( 'pronamic_twinfield_openid_connect_redirect_uri' );

        if ( empty( $client_id ) ) {
            return;
        }

        if ( empty( $client_secret ) ) {
            return;
        }

        if ( empty( $redirect_uri ) ) {
            return;
        }

        $this->openid_connect_client = new OpenIdConnectClient( $client_id, $client_secret, $redirect_uri );
    }
}
