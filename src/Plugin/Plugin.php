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

        \add_action( 'init', array( $this, 'init' ) );
        \add_filter( 'query_vars', array( $this, 'query_vars' ) );
        \add_filter( 'template_include', array( $this, 'template_include' ) );
    }

    /**
     * Initialize.
     *
     * @return void
     */
    public function init() {
        \load_plugin_textdomain( 'pronamic-twinfield', false, dirname( plugin_basename( $this->file ) ) . '/languages' );

        // Rewrites.
        \add_rewrite_rule(
            '^pronamic-twinfield/?$',
            array(
                'pronamic_twinfield_route' => '/',
            ),
            'top'
        );

        \add_rewrite_rule(
            '^pronamic-twinfield/(.*)?',
            array(
                'pronamic_twinfield_route' => '/$matches[1]',
            ),
            'top'
        );
    }

    /**
     * Query vars.
     *
     * @param string[] $query_vars Query vars.
     * @return string[]
     */
    public function query_vars( $query_vars ) {
        $query_vars[] = 'pronamic_twinfield_route';

        return $query_vars;
    }

    /**
     * Template include.
     *
     * @link https://github.com/WordPress/WordPress/blob/5.5/wp-includes/template-loader.php#L97-L113
     * @param string $template Template.
     * @return string|false
     */
    public function template_include( $template ) {
        $route = \get_query_var( 'pronamic_twinfield_route', null );

        if ( null === $route ) {
            return $template;
        }

        $request = new \WP_REST_Request( 'GET', '/pronamic-twinfield/v1' . $route );

        $response = \rest_do_request( $request );

        if ( $response->is_error() ) {
            \wp_die( $response->get_error_message() );
        }

        switch ( $route ) {
            case '/organisation':
                $organisation = $response->get_data();

                include __DIR__ . '/../../templates/organisation.php';
                
                break;
            case '/offices':
                $offices = $response->get_data();

                include __DIR__ . '/../../templates/offices.php';
                
                break;
            default:        
                include __DIR__ . '/../../templates/index.php';
                
                break;
        }

        return false;
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
