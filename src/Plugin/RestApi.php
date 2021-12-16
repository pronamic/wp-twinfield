<?php

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_REST_Request;
use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationTokens;
use Pronamic\WordPress\Twinfield\Authentication\AccessTokenValidation;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Offices\OfficeReadRequest;
use Pronamic\WordPress\Twinfield\Offices\OfficesList;
use Pronamic\WordPress\Twinfield\Offices\OfficesListRequest;
use Pronamic\WordPress\Twinfield\ProcessXmlString;

class RestApi {
	/**
	 * Plugin.
	 *
	 * @var Plugin
	 */
	private $plugin;

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
			'/organisation',
			array(
				'methods'             => 'GET',
				'callback'            => function( WP_REST_Request $request ) {
					return $this->redirect_authorization( 'organisation' );
				},
				'permission_callback' => function () {
					return true;
				},
			)
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/organisation',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_organisation' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(
					'post_id'     => array(
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_offices' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(
					'post_id'     => array(
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'embed'       => array(
						'description'       => 'Embed.',
						'type'              => 'string',
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_office' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(
					'post_id'     => array(
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'office_code'     => array(
						'description'       => 'Twinfield office code.',
						'type'              => 'string',
					),
					/**
					 * Embed?
					 * 
					 * @link https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_embed
					 */
					'embed'       => array(
						'description'       => 'Embed.',
						'type'              => 'string',
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/transactions/(?P<office_code>[a-zA-Z0-9_-]+)/(?P<transaction_type_code>[a-zA-Z0-9_-]+)/(?P<transaction_number>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_transaction' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(
					'post_id'     => array(
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'office_code'     => array(
						'description'       => 'Twinfield office code.',
						'type'              => 'string',
					),
					'transaction_type_code' => array(
						'description'       => 'Twinfield transaction type code.',
						'type'              => 'string',
					),
					'transaction_number' => array(
						'description'       => 'Twinfield transaction number.',
						'type'              => 'string',
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/sales-invoices/(?P<office_code>[a-zA-Z0-9_-]+)/(?P<invoice_type_code>[a-zA-Z0-9_-]+)/(?P<invoice_number>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_sales_invoice' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(
					'post_id'     => array(
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'office_code'     => array(
						'description'       => 'Twinfield office code.',
						'type'              => 'string',
					),
					'invoice_type_code' => array(
						'description'       => 'Twinfield invoice type code.',
						'type'              => 'string',
					),
					'invoice_number' => array(
						'description'       => 'Twinfield invoice number.',
						'type'              => 'string',
					),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/offices',
			array(
				'methods'             => 'GET',
				'callback'            => function( WP_REST_Request $request ) {
					return $this->redirect_authorization( 'offices' );
				},
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

	public function redirect_authorization( $route ) {
		$post = get_post( \get_option( 'pronamic_twinfield_authorization_post_id' ) );

		/**
		 * 303 See Other.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
		 */
		$url = \rest_url( 'pronamic-twinfield/v1/authorizations/' . $post->ID . '/' . $route );

		return new \WP_REST_Response( null, 303, array( 'Location' => $url ) );
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

	public function rest_api_organisation( WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );

		$client = $this->plugin->get_client( $post );

		return $client->get_organisation();
	}

	public function rest_api_offices( WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );

		$client = $this->plugin->get_client( $post );

		$xml_processor = $client->get_xml_processor();

		$request = new OfficesListRequest();

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $request->to_xml() ) );

		$offices = OfficesList::from_xml( (string) $response, $client->get_organisation() );

		/**
		 * Envelope.
		 * 
		 * @link https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_envelope
		 */
		return (object) array(
			'count'     => \iterator_count( $offices ),
			'_embedded' => (object) array(
				'offices'  => $offices,
				'request'  => $request->to_xml(),
				'response' => (string) $response,
			),
		);
	}

	public function rest_api_office( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$request = new OfficeReadRequest( $office_code );

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $request->to_xml() ) );

		$data = array(
			'_embedded' => (object) array(
				'request'  => $request->to_xml(),
				'response' => (string) $response,
			),
		);

		$response = new \WP_REST_Response( $data );

		$response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					array(
						':id' => $post_id,
					)
				)
			),
			array(
				'type' => 'application/hal+json',
			)
		);

		return $response;
	}

	public function rest_api_transaction( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$xml_processor = $client->get_xml_processor();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$request = new \Pronamic\WordPress\Twinfield\Accounting\TransactionReadRequest(
			$request->get_param( 'office_code' ),
			$request->get_param( 'transaction_type_code' ),
			$request->get_param( 'transaction_number' )
		);

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $request->to_xml() ) );

		$data = array(
			'_embedded' => (object) array(
				'request'  => $request->to_xml(),
				'response' => (string) $response,
			),
		);

		$response = new \WP_REST_Response( $data );

		$response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					array(
						':id' => $post_id,
					)
				)
			),
			array(
				'type' => 'application/hal+json',
			)
		);

		return $response;
	}

	public function rest_api_sales_invoice( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$xml_processor = $client->get_xml_processor();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$read_request = new \Pronamic\WordPress\Twinfield\Accounting\SalesInvoiceReadRequest(
			$request->get_param( 'office_code' ),
			$request->get_param( 'invoice_type_code' ),
			$request->get_param( 'invoice_number' )
		);

		$read_response = $xml_processor->process_xml_string( new ProcessXmlString( $read_request->to_xml() ) );

		$sales_invoice = \Pronamic\WordPress\Twinfield\Accounting\SalesInvoice::from_xml( (string) $read_response, $organisation );

		$data = array(
			'office'         => $request->get_param( 'office_code' ),
			'code'           => $request->get_param( 'invoice_type_code' ),
			'invoice_number' => $request->get_param( 'invoice_number' ),
			'resource'       => 'sales_invoice',
		);

		$data['_embedded'] = (object) array(
			'sales_invoice' => $sales_invoice,
			'request_xml'   => $read_request->to_xml(),
			'response_xml'  => (string) $read_response,
		);

		$response = new \WP_REST_Response( $data );

		$response->header( 'X-PronamicTwinfield-ContentType', 'sales-invoice' );

		$response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					array(
						':id' => $post_id,
					)
				)
			),
			array(
				'type' => 'application/hal+json',
			)
		);

		$response->add_link(
			'office',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/offices/:code',
					array(
						':id'   => $post_id,
						':code' => $request->get_param( 'office_code' ),
					)
				)
			),
			array(
				'type' => 'application/hal+json',
			)
		);

		$response->add_link(
			'invoice_type',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/offices/:code/invoice-types/:type',
					array(
						':id'   => $post_id,
						':code' => $request->get_param( 'office_code' ),
						':type' => $request->get_param( 'invoice_type_code' ),
					)
				)
			),
			array(
				'type' => 'application/hal+json',
			)
		);

		return $response;
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
