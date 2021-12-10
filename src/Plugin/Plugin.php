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
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Client;

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

        // AuthorizationPostType
        $this->authorization_post_type = new AuthorizationPostType( $this );
    }

    public function setup() {
        $this->rest_api->setup();

        if ( null !== $this->admin ) {
            $this->admin->setup();
        }

        $this->authorization_post_type->setup();
    }

    public function get_openid_connect_client() {
        $client_id     = \get_option( 'pronamic_twinfield_openid_connect_client_id' );
        $client_secret = \get_option( 'pronamic_twinfield_openid_connect_client_secret' );
        $redirect_uri  = \get_option( 'pronamic_twinfield_openid_connect_redirect_uri' );

        $openid_connect_client = new OpenIdConnectClient( $client_id, $client_secret, $redirect_uri );

        return $openid_connect_client;
    }

    public function save_authentication( $post, $authentication ) {
        return \wp_update_post(
            array(
                'ID'             => $post->ID,
                'post_status'    => 'publish',
                'post_title'     => \sprintf(
                    '%s - %s',
                    $authentication->get_validation()->get_user()->get_organisation()->get_code(),
                    $authentication->get_validation()->get_user()->get_code(),
                ),
                'post_content'   => \wp_json_encode( $authentication, \JSON_PRETTY_PRINT ),
                'post_mime_type' => 'application/json',
            )
        );
    }

    public function get_client( $post ) {
        $openid_connect_client = $this->get_openid_connect_client();

        $authentication = AuthenticationInfo::from_object( \json_decode( $post->post_content ) );

        $client = new Client( $openid_connect_client, $authentication );

        $client->set_authentication_refresh_handler( function( $client ) use ( $post ) {
            $this->save_authentication( $post, $client->get_authentication() );
        } );

        return $client;
    }
}
