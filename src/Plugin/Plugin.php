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

        \add_action( 'init', array( $this, 'init' ), 9 );
        \add_filter( 'query_vars', array( $this, 'query_vars' ) );
        \add_filter( 'template_include', array( $this, 'template_include' ) );

		\add_filter( 'pronamic_twinfield_client', function() {
			return $this->get_client( \get_post( \get_option( 'pronamic_twinfield_authorization_post_id' ) ) );
		} );

		\add_filter( 'redirect_canonical', function( $redirect_url, $requested_url ) {
			$type = \get_query_var( 'pronamic_twinfield_type', null );

			if ( null === $type ) {
				return $redirect_url;
			}

			return $requested_url;
		}, 10, 2 );
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
            '^pronamic-twinfield/(.*)?\.(.*)?$',
            array(
                'pronamic_twinfield_route' => '/$matches[1]',
                'pronamic_twinfield_type'  => '$matches[2]',
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

		// Authorize.
	    $this->maybe_handle_authorize( $_GET );
    }

	public function maybe_handle_authorize( $data ) {
		if ( ! \array_key_exists( 'code', $data ) )  {
			return;
		}

		if ( ! \array_key_exists( 'state', $data ) )  {
			return;
		}

		$url = \add_query_arg(
			array(
				'code' => $data['code'],
			),
			\rest_url( 'pronamic-twinfield/v1/authorize/' . $data['state'] )
		);

		\wp_safe_redirect( $url );

		exit;
	}

    /**
     * Query vars.
     *
     * @param string[] $query_vars Query vars.
     * @return string[]
     */
    public function query_vars( $query_vars ) {
        $query_vars[] = 'pronamic_twinfield_route';
	    $query_vars[] = 'pronamic_twinfield_type';

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

        switch ( $type ) {
            case '/organisation':
                $organisation = $response->get_data();

                include __DIR__ . '/../../templates/organisation.php';
                
                break;
            case '/offices':
                $offices = $response->get_data();

                include __DIR__ . '/../../templates/offices.php';
                
                break;
            default:  
                $data = (object) $response->get_data();

                if ( \is_object( $data ) && \property_exists( $data, 'resource' ) ) {
                    switch ( $data->resource ) {
                        case 'sales_invoice':
                            $sales_invoice = $data->_embedded->sales_invoice;

                            include __DIR__ . '/../../templates/sales-invoice.php';
                
                            break 2;
                    }
                }

                include __DIR__ . '/../../templates/index.php';
                
                break;
        }

        return false;
    }

    public function get_openid_connect_client() {
        $client_id     = \get_option( 'pronamic_twinfield_openid_connect_client_id' );
        $client_secret = \get_option( 'pronamic_twinfield_openid_connect_client_secret' );

        $openid_connect_client = new OpenIdConnectClient( $client_id, $client_secret, \home_url( '/' ) );

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
