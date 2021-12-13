<?php

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_REST_Request;
use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationTokens;
use Pronamic\WordPress\Twinfield\Authentication\AccessTokenValidation;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;

class RestApi {
	/**
	 * Constructs and initialize Twinfield REST API object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	public function setup() {
		// Actions
		\add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * REST API initialize.
	 *
	 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 */
	public function rest_api_init() {
		$namespace = 'pronamic-twinfield/v1';

		register_rest_route(
			$namespace,
			'/authorize/(?P<post_id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_authorize' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(
					'post_id' => array(
						'description' => \__( 'Post ID.', 'pronamic-twinfield' ),
						'type'        => 'string',
						'required'    => true,
					),
					'code'    => array(
						'description' => \__( 'Code.', 'pronamic-twinfield' ),
						'type'        => 'string',
						'required'    => true,
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/browse/fields',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_browse_fields' ),
				'permission_callback' => function () {
					return true;
				},
			)
		);

		register_rest_route(
			$namespace,
			'/offices',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_offices' ),
				'permission_callback' => function () {
					return true;
				},
			)
		);

		register_rest_route(
			$namespace,
			'/customers/list',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_customers_list' ),
				'permission_callback' => function () {
					return true;
				},
			)
		);

		register_rest_route(
			$namespace,
			'/customers',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_customers' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(
					'page'     => array(
						'description'       => 'Current page of the collection.',
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					),
					'per_page' => array(
						'description'       => 'Maximum number of items to be returned in result set.',
						'type'              => 'integer',
						'default'           => 10,
						'sanitize_callback' => 'absint',
					),
					'search'   => array(
						'description'       => 'Limit results to those matching a string.',
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/articles',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_articles' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(
					'page'     => array(
						'description'       => 'Current page of the collection.',
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					),
					'per_page' => array(
						'description'       => 'Maximum number of items to be returned in result set.',
						'type'              => 'integer',
						'default'           => 10,
						'sanitize_callback' => 'absint',
					),
					'search'   => array(
						'description'       => 'Limit results to those matching a string.',
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);
	}

	public function rest_api_authorize( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );
		$code    = $request->get_param( 'code' );

        $openid_connect_client = $this->plugin->get_openid_connect_client(); 

		$response = $openid_connect_client->get_access_token( $code );

		$tokens = AuthenticationTokens::from_object( $response );

		$response = $openid_connect_client->get_access_token_validation( $tokens->get_access_token() );

		$validation = AccessTokenValidation::from_object( $response );

		$authentication = new AuthenticationInfo( $tokens, $validation );

		$result = $this->plugin->save_authentication( \get_post( $post_id ), $authentication );

		/**
		 * 303 See Other.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
		 */
		$url = \add_query_arg(
			array(
				'post' => $post_id,
				'action' => 'edit',
			),
			admin_url( 'post.php' )
		);

		return new \WP_REST_Response( null, 303, array( 'Location' => $url ) );
	}

	public function rest_api_browse_fields( WP_REST_Request $request ) {
		$client = $this->plugin->get_client();

		$xml_processor = $client->get_xml_processor();
		
	}

	public function rest_api_offices( WP_REST_Request $request ) {
		$post = get_post( \get_option( 'pronamic_twinfield_authorization_post_id' ) );

		$client = $this->plugin->get_client( $post );

		return $client->get_offices();
	}

	public function rest_api_customers_list( WP_REST_Request $request ) {
		$client = $this->plugin->get_client();

		$xml_processor = $client->get_xml_processor();

		$customer_service = new \Pronamic\WP\Twinfield\Customers\CustomerService( $xml_processor );

		$customers = $customer_service->get_customers( get_option( 'twinfield_default_office_code' ) );

		if ( ! is_array( $customers ) ) {
			return array();
		}

		if ( empty( $customers ) ) {
			return array();
		}

		return $customers;
	}

	public function rest_api_customers( WP_REST_Request $request ) {
		$pattern = $request->get_param( 'search' );
		$pattern = empty( $pattern ) ? '*' : '*' . $pattern . '*';

		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );

		$first_row = ( ( $page - 1 ) * $per_page ) + 1;
		$max_rows  = $per_page;

		$client = $this->plugin->get_client();

		$customers_finder = new \Pronamic\WP\Twinfield\Customers\CustomerFinder( $client->get_finder() );

		$customers = $customers_finder->get_customers( $pattern, 0, $first_row, $max_rows );

		if ( ! is_array( $customers ) ) {
			return array();
		}

		if ( empty( $customers ) ) {
			return array();
		}

		$options = array();

		foreach ( $customers as $customer ) {
			$option       = new \stdClass();
			$option->code = $customer->get_code();
			$option->name = $customer->get_name();

			$options[] = $option;
		}

		return $options;
	}

	public function rest_api_articles( WP_REST_Request $request ) {
		$pattern = $request->get_param( 'search' );
		$pattern = empty( $pattern ) ? '*' : '*' . $pattern . '*';

		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );

		$first_row = ( ( $page - 1 ) * $per_page ) + 1;
		$max_rows  = $per_page;

		$client = $this->plugin->get_client();

		$articles_finder = new \Pronamic\WP\Twinfield\Articles\ArticlesFinder( $client->get_finder() );

		$articles = $articles_finder->get_articles( $pattern, 0, $first_row, $max_rows );

		if ( ! is_array( $articles ) ) {
			return array();
		}

		if ( empty( $articles ) ) {
			return array();
		}

		$options = array();

		foreach ( $articles as $article ) {
			$option       = new \stdClass();
			$option->code = $article->get_code();
			$option->name = $article->get_name();

			$options[] = $option;
		}

		return $options;
	}
}
