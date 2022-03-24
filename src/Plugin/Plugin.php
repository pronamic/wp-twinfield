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
use WP_Post;

/**
 * Plugin
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Plugin {
	/**
	 * Construct plugin.
	 *
	 * @param string $file Plugin file.
	 */
	public function __construct( $file ) {
		$this->file = $file;

		$this->rest_api = new RestApi( $this );

		if ( is_admin() ) {
			$this->admin = new Admin( $this );
		}

		$this->authorization_post_type = new AuthorizationPostType( $this );
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		$this->rest_api->setup();

		if ( null !== $this->admin ) {
			$this->admin->setup();
		}

		$this->authorization_post_type->setup();

		\add_action( 'init', [ $this, 'init' ], 9 );
		\add_filter( 'query_vars', [ $this, 'query_vars' ] );
		\add_filter( 'template_include', [ $this, 'template_include' ] );

		\add_filter(
			'pronamic_twinfield_client',
			function() {
				return $this->get_client( \get_post( \get_option( 'pronamic_twinfield_authorization_post_id' ) ) );
			}
		);

		\add_filter(
			'redirect_canonical',
			function( $redirect_url, $requested_url ) {
				$type = \get_query_var( 'pronamic_twinfield_type', null );

				if ( null === $type ) {
					return $redirect_url;
				}

				return $requested_url;
			},
			10,
			2
		);
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
			[
				'pronamic_twinfield_route' => '/',
			],
			'top'
		);

		\add_rewrite_rule(
			'^pronamic-twinfield/(.*)?\.(.*)?$',
			[
				'pronamic_twinfield_route' => '/$matches[1]',
				'pronamic_twinfield_type'  => '$matches[2]',
			],
			'top'
		);

		\add_rewrite_rule(
			'^pronamic-twinfield/(.*)?',
			[
				'pronamic_twinfield_route' => '/$matches[1]',
			],
			'top'
		);

		// Authorize.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->maybe_handle_authorize( $_GET );
	}

	/**
	 * Maybe handle authorize.
	 *
	 * @param array $data Data.
	 */
	public function maybe_handle_authorize( $data ) {
		if ( ! \array_key_exists( 'code', $data ) ) {
			return;
		}

		if ( ! \array_key_exists( 'state', $data ) ) {
			return;
		}

		$url = \add_query_arg(
			[
				'code' => $data['code'],
			],
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
		$type  = \get_query_var( 'pronamic_twinfield_type', 'html' );

		if ( null === $route ) {
			return $template;
		}

		$request = new \WP_REST_Request( 'GET', '/pronamic-twinfield/v1' . $route );

		$response = \rest_do_request( $request );

		if ( $response->is_error() ) {
			$error = $response->as_error();

			\wp_die( \esc_html( $error->get_error_message() ) );
		}

		switch ( $type ) {
			default:
				$data = (object) $response->get_data();

				if ( \property_exists( $data, 'type' ) ) {
					switch ( $data->type ) {
						case 'organisation':
							$organisation = $data->data;

							include __DIR__ . '/../../templates/organisation.php';

							return false;
						case 'offices':
							$offices = $data->data;

							include __DIR__ . '/../../templates/offices.php';

							return false;
						case 'office':
							$office = $data->data;

							include __DIR__ . '/../../templates/office.php';

							return false;
						case 'transaction':
							$transaction = $data->data;

							include __DIR__ . '/../../templates/transaction.php';

							return false;
						case 'sales_invoice':
							$sales_invoice = $data->data;

							$customer = null;

							$links = $response->get_links();

							$rest_request = \WP_REST_Request::from_url( $links['customer'][0]['href'] );

							$rest_response = \rest_do_request( $rest_request );

							if ( ! $rest_response->is_error() ) {
								$customer_data = $rest_response->get_data();

								$customer = $customer_data['data'];
							}

							switch ( $type ) {
								case 'pdf':
									\ob_start();

									include __DIR__ . '/../../templates/sales-invoice-pdf-html.php';

									$html = \ob_get_clean();

									$mpdf = new \Mpdf\Mpdf();
									$mpdf->WriteHTML( $html );
									$mpdf->Output(
										\sprintf(
											'Pronamic factuur %s.pdf',
											$sales_invoice->get_header()->get_number()
										),
										\Mpdf\Output\Destination::INLINE
									);

									exit;
								case 'html-pdf':
									include __DIR__ . '/../../templates/sales-invoice-pdf-html.php';

									break;
								case 'xml':
									/**
									 * Difference between text/xml and application/xml.
									 *
									 * @link https://stackoverflow.com/questions/3272534/what-content-type-value-should-i-send-for-my-xml-sitemap
									 * @link http://www.grauw.nl/blog/entry/489/
									 */
									header( 'Content-Type: application/xml' );

									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									echo $data->_embedded->response_xml;

									exit;
								case 'html':
								default:
									include __DIR__ . '/../../templates/sales-invoice.php';

									return false;
							}
					}
				}

				include __DIR__ . '/../../templates/index.php';
				
				break;
		}

		return false;
	}

	/**
	 * Get OpenID connect client.
	 *
	 * @return OpenIdConnectClient
	 */
	public function get_openid_connect_client() {
		$client_id     = \get_option( 'pronamic_twinfield_openid_connect_client_id' );
		$client_secret = \get_option( 'pronamic_twinfield_openid_connect_client_secret' );

		$openid_connect_client = new OpenIdConnectClient( $client_id, $client_secret, \home_url( '/' ) );

		return $openid_connect_client;
	}

	/**
	 * Save authentication.
	 *
	 * @param WP_Post            $post           Post.
	 * @param AuthenticationInfo $authentication Authentication.
	 * @return int|\WP_Error
	 */
	public function save_authentication( $post, $authentication ) {
		return \wp_update_post(
			[
				'ID'             => $post->ID,
				'post_status'    => 'publish',
				'post_title'     => \sprintf(
					'%s - %s',
					$authentication->get_validation()->get_user()->get_organisation()->get_code(),
					$authentication->get_validation()->get_user()->get_code(),
				),
				'post_content'   => \wp_json_encode( $authentication, \JSON_PRETTY_PRINT ),
				'post_mime_type' => 'application/json',
			]
		);
	}

	/**
	 * Get client by post.
	 *
	 * @param WP_Post $post Post.
	 * @return Client
	 */
	public function get_client( $post ) {
		$openid_connect_client = $this->get_openid_connect_client();

		$authentication = AuthenticationInfo::from_object( \json_decode( $post->post_content ) );

		$client = new Client( $openid_connect_client, $authentication );

		$client->set_authentication_refresh_handler(
			function( $client ) use ( $post ) {
				$this->save_authentication( $post, $client->get_authentication() );
			}
		);

		return $client;
	}
}
