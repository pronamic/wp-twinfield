<?php
/**
 * REST API
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Suppliers\Supplier;
use WP_REST_Request;
use Pronamic\WordPress\Twinfield\Dimensions\Dimension;
use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationTokens;
use Pronamic\WordPress\Twinfield\Authentication\AccessTokenValidation;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Offices\OfficeReadRequest;
use Pronamic\WordPress\Twinfield\Offices\OfficesList;
use Pronamic\WordPress\Twinfield\Offices\OfficesListRequest;
use Pronamic\WordPress\Twinfield\ProcessXmlString;
use Pronamic\WordPress\Twinfield\Finder\Search;
use Pronamic\WordPress\Twinfield\Twinfield;

/**
 * REST API
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
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
		\add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );
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
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_authorize' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id' => [
						'description' => \__( 'Post ID.', 'pronamic-twinfield' ),
						'type'        => 'string',
						'required'    => true,
					],
					'code'    => [
						'description' => \__( 'Code.', 'pronamic-twinfield' ),
						'type'        => 'string',
						'required'    => true,
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/browse/(?P<browse_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_browse_fields' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'     => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'office_code' => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
					'browse_code' => [
						'description' => 'Twinfield browse code.',
						'type'        => 'string',
					],
				],
			]
		);

		$browse_query_args = [
			'post_id'     => [
				'description'       => 'Authorization post ID.',
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			],
			'office_code' => [
				'description' => 'Twinfield office code.',
				'type'        => 'string',
			],
			'browse_code' => [
				'description' => 'Twinfield browse code.',
				'type'        => 'string',
			],
		];

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/browse/(?P<browse_code>[a-zA-Z0-9_-]+)/query',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_browse_query' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => $browse_query_args,
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/browse/query/(?P<office_code>[a-zA-Z0-9_-]+)/(?P<browse_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_browse_query' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => $browse_query_args,
			]
		);

		register_rest_route(
			$namespace,
			'/organisation',
			[
				'methods'             => 'GET',
				'callback'            => function( WP_REST_Request $request ) {
					return $this->redirect_authorization( 'organisation' );
				},
				'permission_callback' => function () {
					return true;
				},
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/organisation',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_organisation' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id' => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_offices' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id' => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'embed'   => [
						'description' => 'Embed.',
						'type'        => 'string',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/finder-types',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_finder_types' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id' => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		$finder_route_args = function( $callback = null, $type = null ) {
			$callback = ( null === $callback ) ? [ $this, 'rest_api_finder' ] : $callback;

			$args = [
				'post_id'     => [
					'description'       => 'Authorization post ID.',
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				],
				'pattern'     => [
					'description' => 'The search pattern. May contain wildcards * and ?.',
					'type'        => 'string',
					'required'    => true,
					'default'     => '*',
				],
				'field'       => [
					'description' => 'Fields to search through, see Search fields.',
					'type'        => 'int',
					'required'    => true,
					'default'     => 0,
				],
				'first_row'   => [
					'description' => 'First row to return, usefull for paging.',
					'type'        => 'int',
					'required'    => true,
					'default'     => 1,
				],
				'max_rows'    => [
					'description' => 'Maximum number of rows to return, usefull for paging.',
					'type'        => 'int',
					'required'    => true,
					'default'     => 10,
				],
				'office_code' => [
					'description' => 'Twinfield office code.',
					'type'        => 'string',
				],
			];

			if ( null === $type ) {
				$args['type'] = [
					'description' => 'Finder type.',
					'type'        => 'string',
					'required'    => true,
				];
			}

			return [
				'methods'             => 'GET',
				'callback'            => $callback,
				'permission_callback' => function () {
					return true;
				},
				'args'                => $args,
			];
		};

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/finder/(?P<type>[a-zA-Z0-9_-]+)',
			$finder_route_args()
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/finder/(?P<type>[a-zA-Z0-9_-]+)',
			$finder_route_args()
		);

		$finder_types = [
			[
				'slug'    => 'suppliers',
				'type'    => 'DIM',
				'dimtype' => 'CRD',
			],
			[
				'slug'    => 'customers',
				'type'    => 'DIM',
				'dimtype' => 'DEB',
			],
			[
				'slug'    => 'cost-centers',
				'type'    => 'DIM',
				'dimtype' => 'KPL',
			],
			[
				'slug'    => 'fixed-assets',
				'type'    => 'DIM',
				'dimtype' => 'KPL',
			],
			[
				'slug'    => 'projects',
				'type'    => 'DIM',
				'dimtype' => 'PRJ',
			],
			[
				'slug'    => 'activities',
				'type'    => 'DIM',
				'dimtype' => 'AST',
			],
			[
				'slug'    => 'dimension-groups',
				'type'    => 'GRP',
				'dimtype' => null,
			],
			[
				'slug'    => 'dimension-types',
				'type'    => 'DMT',
				'dimtype' => null,
			],
			[
				'slug'    => 'asset-methods',
				'type'    => 'ASM',
				'dimtype' => null,
			],
			[
				'slug'    => 'offices',
				'type'    => 'OFF',
				'dimtype' => null,
			],
			[
				'slug'    => 'users',
				'type'    => 'USR',
				'dimtype' => null,
			],
			[
				'slug'    => 'articles',
				'type'    => 'ART',
				'dimtype' => null,
			],
			[
				'slug'    => 'currencies',
				'type'    => 'CUR',
				'dimtype' => null,
			],
			[
				'slug'    => 'rates',
				'type'    => 'TRT',
				'dimtype' => null,
			],
			[
				'slug'    => 'vat',
				'type'    => 'VAT',
				'dimtype' => null,
			],
			[
				'slug'    => 'hierarchies',
				'type'    => 'HIE',
				'dimtype' => null,
			],
		];

		foreach ( $finder_types as $finder_type ) {
			$slug    = $finder_type['slug'];
			$type    = $finder_type['type'];
			$dimtype = $finder_type['dimtype'];

			register_rest_route(
				$namespace,
				'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/' . $slug,
				$finder_route_args(
					function( WP_REST_Request $request ) use ( $type, $dimtype ) {
						$request->set_param( 'type', $type );
						$request->set_param( 'dimtype', $dimtype );

						$response = $this->rest_api_finder( $request );

						switch ( $type ) {
							case 'DIM':
								switch ( $dimtype ) {
									case 'CRD':
										$data = array();

										foreach ( $response->get_data() as $item ) {
											$supplier = new Supplier( $dimtype, $item[0] );
											$supplier->set_name( $item[1] );

											$data[] = $supplier;
										}

										return $data;
								}
						}

						return $response;
					},
					$type
				)
			);
		}

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/hierarchies/(?P<hierarchy_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_hierarchy' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'        => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'office_code'    => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
					'hierarchy_code' => [
						'description' => 'Twinfield hierarchy code.',
						'type'        => 'string',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/deleted-transactions',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_deleted_transactions' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'     => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
					],
					'office_code' => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
						'required'    => true,
					],
					'daybook'     => [
						'description' => 'Deleted daybook (transaction type). Optional.',
						'type'        => 'string',
					],
					'date_from'   => [
						'description' => 'The Date from which deleted transactions should be read. Optional.',
						'type'        => 'string',
					],
					'date_to'     => [
						'description' => 'The Date to which deleted transactions should be read. Optional.',
						'type'        => 'string',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/years',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_years' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'     => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
					],
					'office_code' => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
						'required'    => true,
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/periods/(?P<year>\d{4})',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_periods' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'     => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
					],
					'office_code' => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
						'required'    => true,
					],
					'year'        => [
						'description' => 'Year.',
						'type'        => 'int',
						'required'    => true,
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/declarations',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_declarations' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'     => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'office_code' => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_office' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'     => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'office_code' => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
					/**
					 * Embed?
					 * 
					 * @link https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_embed
					 */
					'embed'       => [
						'description' => 'Embed.',
						'type'        => 'string',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/transactions/(?P<office_code>[a-zA-Z0-9_-]+)/(?P<transaction_type_code>[a-zA-Z0-9_-]+)/(?P<transaction_number>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_transaction' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'               => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'office_code'           => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
					'transaction_type_code' => [
						'description' => 'Twinfield transaction type code.',
						'type'        => 'string',
					],
					'transaction_number'    => [
						'description' => 'Twinfield transaction number.',
						'type'        => 'string',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/sales-invoices/(?P<office_code>[a-zA-Z0-9_-]+)/(?P<invoice_type_code>[a-zA-Z0-9_-]+)/(?P<invoice_number>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_sales_invoice' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'           => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'office_code'       => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
					'invoice_type_code' => [
						'description' => 'Twinfield invoice type code.',
						'type'        => 'string',
					],
					'invoice_number'    => [
						'description' => 'Twinfield invoice number.',
						'type'        => 'string',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/dimensions/(?P<office_code>[a-zA-Z0-9_-]+)/(?P<dimension_type_code>[a-zA-Z0-9_-]+)/(?P<dimension_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_dimension' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'             => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'office_code'         => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
					'dimension_type_code' => [
						'description' => 'Twinfield dimension type code.',
						'type'        => 'string',
					],
					'dimension_code'      => [
						'description' => 'Twinfield dimension code.',
						'type'        => 'string',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/offices',
			[
				'methods'             => 'GET',
				'callback'            => function( WP_REST_Request $request ) {
					return $this->redirect_authorization( 'offices' );
				},
				'permission_callback' => function () {
					return true;
				},
			]
		);

		register_rest_route(
			$namespace,
			'/customers/list',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_customers_list' ],
				'permission_callback' => function () {
					return true;
				},
			]
		);

		register_rest_route(
			$namespace,
			'/customers',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_customers' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'page'     => [
						'description'       => 'Current page of the collection.',
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
					'per_page' => [
						'description'       => 'Maximum number of items to be returned in result set.',
						'type'              => 'integer',
						'default'           => 10,
						'sanitize_callback' => 'absint',
					],
					'search'   => [
						'description'       => 'Limit results to those matching a string.',
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/articles',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_articles' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'page'     => [
						'description'       => 'Current page of the collection.',
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
					'per_page' => [
						'description'       => 'Maximum number of items to be returned in result set.',
						'type'              => 'integer',
						'default'           => 10,
						'sanitize_callback' => 'absint',
					],
					'search'   => [
						'description'       => 'Limit results to those matching a string.',
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
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

		return new \WP_REST_Response( null, 303, [ 'Location' => $url ] );
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
			[
				'post'   => $post_id,
				'action' => 'edit',
			],
			admin_url( 'post.php' )
		);

		return new \WP_REST_Response( null, 303, [ 'Location' => $url ] );
	}

	public function rest_api_organisation( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );
		
		$post = \get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$rest_response = new \WP_REST_Response(
			[
				'type' => 'organisation',
				'data' => $organisation,
			]
		);

		$rest_response->add_link(
			'offices',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/offices',
					[
						':id' => $post_id,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		return $rest_response;
	}

	public function rest_api_offices( WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );

		$client = $this->plugin->get_client( $post );

		$xml_processor = $client->get_xml_processor();

		$offices_list_request = new OfficesListRequest();

		$offices_list_response = $xml_processor->process_xml_string( new ProcessXmlString( $offices_list_request->to_xml() ) );

		$offices = OfficesList::from_xml( (string) $offices_list_response, $client->get_organisation() );

		/**
		 * Envelope.
		 * 
		 * @link https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_envelope
		 * @link https://jsonapi.org/format/#document-top-level
		 */
		$rest_response = new \WP_REST_Response(
			[
				'type'      => 'offices',
				'data'      => $offices,
				'_embedded' => (object) [
					'request'  => (string) $offices_list_request,
					'response' => (string) $offices_list_response,
				],
			]
		);

		$rest_response->add_link( 'self', \rest_url( $request->get_route() ) );

		$rest_response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					[
						':id' => $post_id,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		return $rest_response;
	}

	public function rest_api_finder_types( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$twinfield = new Twinfield();

		$finder_types = $twinfield->get_finder_types();

		$data = array();

		foreach ( $finder_types as $type => $label ) {
			$data[] = [
				'type'   => $type,
				'label'  => $label,
				'_links' => [
					'self' => [ 
						[
							'href' => rest_url(
								strtr(
									'pronamic-twinfield/v1/authorizations/:id/finder/:type',
									[
										':id'   => $post_id,
										':type' => $type,
									]
								)
							),
						],
					],
				],
			];
		}

		return $data;
	}

	public function rest_api_finder( WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$finder = $client->get_finder();

		$type      = $request->get_param( 'type' );
		$pattern   = $request->get_param( 'pattern' );
		$field     = $request->get_param( 'field' );
		$first_row = $request->get_param( 'first_row' );
		$max_rows  = $request->get_param( 'max_rows' );

		$options = array();

		/**
		 * Office.
		 * 
		 * Since it is not possible to add the company code
		 * to the finder, make sure the correct company is
		 * set by using either the SelectCompany function
		 * or adding the office option.
		 * 
		 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Miscellaneous/Finder
		 */
		$office_code = $request->get_param( 'office_code' );

		if ( ! empty( $office_code ) ) {
			$office = $organisation->new_office( $office_code );

			$finder->set_office( $office );

			$options['office'] = $office_code;
		}

		/**
		 * Dimension type.
		 */
		$dimension_type = $request->get_param( 'dimtype' );

		if ( ! empty( $dimension_type ) ) {
			$options['dimtype'] = $dimension_type;
		}

		$search = new Search( $type, $pattern, $field, $first_row, $max_rows, $options );

		$response = $finder->search( $search );

		return $response;
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

		$data = [
			'type'      => 'office',
			'data'      => \Pronamic\WordPress\Twinfield\Offices\Office::from_xml( (string) $response, $office ),
			'_embedded' => (object) [
				'request'  => $request->to_xml(),
				'response' => (string) $response,
			],
		];

		$response = new \WP_REST_Response( $data );

		$response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					[
						':id' => $post_id,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		return $response;
	}

	public function rest_api_hierarchy( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$hierarchies_service = $client->get_service( 'hierarchies' );

		$hierarchies_service->set_office( $office );

		$hierarchy_code = $request->get_param( 'hierarchy_code' );

		$hierarchy = $hierarchies_service->get_hierarchy( $hierarchy_code );

		return $hierarchy;
	}

	public function rest_api_deleted_transactions( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$deleted_transactions_service = $client->get_service( 'deleted-transactions' );

		$deleted_transactions_service->set_office( $office );

		$deleted_transactions = $deleted_transactions_service->get_deleted_transactions( $office_code );

		return $deleted_transactions;
	}

	public function rest_api_years( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$periods_service = $client->get_service( 'periods' );

		$periods_service->set_office( $office );

		$years = $periods_service->get_years( $office );

		$results = [];

		foreach ( $years as $year ) {
			$results[] = [
				'year'   => $year,
				'_links' => [
					'self' => [
						[
							'href' => \rest_url( 
								\strtr(
									'pronamic-twinfield/v1/authorizations/:auth_post_id/offices/:office_code/periods/:year',
									[
										':auth_post_id' => $request->get_param( 'post_id' ),
										':office_code'  => $office->get_code(),
										':year'         => $year,
									]
								)
							),
						],
					],
				],
			];
		}

		return $results;
	}

	public function rest_api_periods( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$periods_service = $client->get_service( 'periods' );

		$periods_service->set_office( $office );

		$year = $request->get_param( 'year' );

		$periods = $periods_service->get_periods( $office, $year );

		return $periods;
	}

	public function rest_api_declarations( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$declarations_service = $client->get_service( 'declarations' );

		$summaries = $declarations_service->get_all_summaries( $office );

		return $summaries;
	}

	public function rest_api_transaction( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$request = new \Pronamic\WordPress\Twinfield\Transactions\TransactionReadRequest(
			$request->get_param( 'office_code' ),
			$request->get_param( 'transaction_type_code' ),
			$request->get_param( 'transaction_number' )
		);

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $request->to_xml() ) );

		$transaction_unserializer = new \Pronamic\WordPress\Twinfield\XML\Transactions\TransactionUnserializer( $organisation );

		$transaction = $transaction_unserializer->unserialize_string( (string) $response );

		$data = [
			'type'      => 'transaction',
			'data'      => $transaction,
			'_embedded' => (object) [
				'request'  => $request->to_xml(),
				'response' => (string) $response,
			],
		];

		$response = new \WP_REST_Response( $data );

		$response->add_link(
			'transaction_type',
			\rest_url(
				\strtr(
					'pronamic-twinfield/v1/authorizations/:id/transaction-types/:office_code/:transaction_type_code',
					[
						':id'                    => $post_id,
						':office_code'           => $transaction->get_office()->get_code(),
						':transaction_type_code' => $transaction->get_transaction_type()->get_code(),
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		$response->add_link(
			'office',
			\rest_url(
				\strtr(
					'pronamic-twinfield/v1/authorizations/:id/offices/:office_code',
					[
						':id'          => $post_id,
						':office_code' => $transaction->get_office()->get_code(),
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		$response->add_link(
			'organisation',
			\rest_url(
				\strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					[
						':id' => $post_id,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
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

		$read_request = new \Pronamic\WordPress\Twinfield\SalesInvoices\SalesInvoiceReadRequest(
			$request->get_param( 'office_code' ),
			$request->get_param( 'invoice_type_code' ),
			$request->get_param( 'invoice_number' )
		);

		$read_response = $xml_processor->process_xml_string( new ProcessXmlString( $read_request->to_xml() ) );

		$sales_invoice = \Pronamic\WordPress\Twinfield\SalesInvoices\SalesInvoice::from_xml( (string) $read_response, $organisation );

		$data = [
			'office'         => $request->get_param( 'office_code' ),
			'code'           => $request->get_param( 'invoice_type_code' ),
			'invoice_number' => $request->get_param( 'invoice_number' ),
			'type'           => 'sales_invoice',
			'data'           => $sales_invoice,
		];

		$data['_embedded'] = (object) [
			'request_xml'  => $read_request->to_xml(),
			'response_xml' => (string) $read_response,
		];

		$response = new \WP_REST_Response( $data );

		$response->header( 'X-PronamicTwinfield-ContentType', 'sales-invoice' );

		$response->add_link(
			'self',
			\rest_url( $request->get_route() ),
			[
				'type' => 'application/hal+json',
			]
		);

		$response->add_link(
			'pdf',
			\home_url( $request->get_route() . '.pdf' ),
			[
				'type' => 'application/pdf',
			]
		);

		$response->add_link(
			'xml',
			\home_url( $request->get_route() . '.xml' ),
			[
				'type' => 'application/xml',
			]
		);

		$response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					[
						':id' => $post_id,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		$response->add_link(
			'office',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/offices/:code',
					[
						':id'   => $post_id,
						':code' => $request->get_param( 'office_code' ),
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		$response->add_link(
			'invoice_type',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/offices/:code/invoice-types/:type',
					[
						':id'   => $post_id,
						':code' => $request->get_param( 'office_code' ),
						':type' => $request->get_param( 'invoice_type_code' ),
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		$response->add_link(
			'customer',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/dimensions/:office_code/:dimension_type_code/:dimension_code',
					[
						':id'                  => $post_id,
						':office_code'         => $request->get_param( 'office_code' ),
						':dimension_type_code' => 'DEB',
						':dimension_code'      => $sales_invoice->get_header()->get_customer(),
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		return $response;
	}

	/**
	 * /authorizations/(?P<post_id>\d+)/dimensions/(?P<office_code>[a-zA-Z0-9_-]+)/(?P<dimension_type_code>[a-zA-Z0-9_-]+)/(?P<dimension_code>[a-zA-Z0-9_-]+)
	 */
	public function rest_api_dimension( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$xml_processor = $client->get_xml_processor();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$dimension_read_request = new \Pronamic\WordPress\Twinfield\Dimensions\DimensionReadRequest(
			$request->get_param( 'office_code' ),
			$request->get_param( 'dimension_type_code' ),
			$request->get_param( 'dimension_code' )
		);

		$dimension_read_response = $xml_processor->process_xml_string( new ProcessXmlString( $dimension_read_request->to_xml() ) );

		$dimension = Dimension::from_xml( (string) $dimension_read_response, $office );

		$data = [
			'type'      => 'dimension',
			'data'      => $dimension,
			'_embedded' => (object) [
				'request'  => $dimension_read_request->to_xml(),
				'response' => (string) $dimension_read_response,
			],
		];

		$rest_response = new \WP_REST_Response( $data );

		$rest_response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					[
						':id' => $post_id,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		$rest_response->add_link(
			'office',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/offices/:code',
					[
						':id'   => $post_id,
						':code' => $request->get_param( 'office_code' ),
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		$rest_response->add_link(
			'dimension_type',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/offices/:code/dimension-types/:type',
					[
						':id'   => $post_id,
						':code' => $request->get_param( 'office_code' ),
						':type' => $request->get_param( 'dimension_type_code' ),
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		return $rest_response;
	}

	public function rest_api_customers_list( WP_REST_Request $request ) {
		$client = $this->plugin->get_client();

		$xml_processor = $client->get_xml_processor();

		$customer_service = new \Pronamic\WP\Twinfield\Customers\CustomerService( $xml_processor );

		$customers = $customer_service->get_customers( get_option( 'twinfield_default_office_code' ) );

		if ( ! is_array( $customers ) ) {
			return [];
		}

		if ( empty( $customers ) ) {
			return [];
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
			return [];
		}

		if ( empty( $customers ) ) {
			return [];
		}

		$options = [];

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
			return [];
		}

		if ( empty( $articles ) ) {
			return [];
		}

		$options = [];

		foreach ( $articles as $article ) {
			$option       = new \stdClass();
			$option->code = $article->get_code();
			$option->name = $article->get_name();

			$options[] = $option;
		}

		return $options;
	}

	public function rest_api_browse_fields( WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$document = new \DOMDocument();

		$browse_code = $request->get_param( 'browse_code' );

		$read_element = $document->appendChild( $document->createElement( 'read' ) );

		$read_element->appendChild( $document->createElement( 'type', 'browse' ) );
		$read_element->appendChild( $document->createElement( 'office', $office_code ) );
		$read_element->appendChild( $document->createElement( 'code', $browse_code ) );

		$xml = $document->saveXML( $document->documentElement );

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $xml ) );

		$rest_response = new \WP_REST_Response(
			[
				'type'      => 'browse',
				'data'      => $offices,
				'_embedded' => (object) [
					'request'  => (string) $xml,
					'response' => (string) $response,
				],
			]
		);

		$rest_response->add_link( 'self', \rest_url( $request->get_route() ) );

		return $rest_response;      
	}

	public function rest_api_browse_query( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->new_office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$document = new \DOMDocument();

		$browse_code = $request->get_param( 'browse_code' );

		$columns_element = $document->appendChild( $document->createElement( 'columns' ) );
		$columns_element->setAttribute( 'code', $browse_code );

		$optimize = $request->get_param( 'optimize' );

		if ( $optimize ) {
			$columns_element->setAttribute( 'optimize', 'true' );
		}

		$values   = (array) $request->get_param( 'values' );
		$visibles = (array) $request->get_param( 'visibles' );

		$fields = \array_keys( \array_merge( $values, $visibles ) );

		foreach ( $fields as $field ) {
			$column_element = $columns_element->appendChild( $document->createElement( 'column' ) );
			$column_element->appendChild( $document->createElement( 'field', $field ) );

			if ( \array_key_exists( $field, $values ) ) {
				$value = $values[ $field ];

				$operator = 'equal';
				$from     = $value;
				$to       = null;

				/**
				 * @link https://docs.github.com/en/search-github/getting-started-with-searching-on-github/understanding-the-search-syntax#query-for-values-between-a-range
				 */
				$between_position = \mb_strpos( $value, '..' );

				if ( false !== $between_position ) {
					$operator = 'between';
					$from     = \mb_substr( $value, 0, $between_position );
					$to       = \mb_substr( $value, $between_position + 2 );
				}

				$from_date = \DateTimeImmutable::createFromFormat( 'Y-m-d', $from );

				if ( false !== $from_date ) {
					$from = $from_date->format( 'Ymd' );
				}

				$to_date = \DateTimeImmutable::createFromFormat( 'Y-m-d', $to );

				if ( false !== $to_date ) {
					$to = $to_date->format( 'Ymd' );
				}

				$column_element->appendChild( $document->createElement( 'operator', $operator ) );
				$column_element->appendChild( $document->createElement( 'from', $from ) );

				if ( ! empty( $to ) ) {
					$column_element->appendChild( $document->createElement( 'to', $to ) );
				}
			}

			$visible = false;

			if ( \array_key_exists( $field, $visibles ) ) {
				$visible = (bool) $visibles[ $field ];
			}

			if ( true === $visible ) {
				$column_element->appendChild( $document->createElement( 'visible', 'true' ) );
			}
		}

		$xml = $document->saveXML( $document->documentElement );

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $xml ) );

		$unserializer = new \Pronamic\WordPress\Twinfield\Browse\BrowseDataUnserializer( $organisation );

		$transaction_lines = $unserializer->unserialize( (string) $response );

		$data = array();

		foreach ( $transaction_lines as $transaction_line ) {
			$object = (array) $transaction_line->jsonSerialize();

			$transaction      = $transaction_line->get_transaction();
			$transaction_type = $transaction->get_transaction_type();
			$office           = $transaction_type->get_office();
			$organisation     = $office->get_organisation();

			$object['_links'] = [
				'transaction' => [
					'href' => rest_url(
						strtr(
							'pronamic-twinfield/v1/authorizations/:id/transactions/:office_code/:transaction_type_code/:transaction_number',
							[
								':id'                    => $post_id,
								':office_code'           => $office->get_code(),
								':transaction_type_code' => $transaction_type->get_code(),
								':transaction_number'    => $transaction->get_number(),
							]
						)
					),
				],
			];

			$data[] = $object;
		}

		$rest_response = new \WP_REST_Response(
			[
				'type'      => 'browse',
				'data'      => $data,
				'_embedded' => (object) [
					'request'  => (string) $xml,
					'response' => (string) $response,
				],
			]
		);

		$rest_response->add_link( 'self', \rest_url( $request->get_route() ) );

		$rest_response->add_link(
			'fields',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/browse/fields/:office_code/:browse_code/',
					[
						':id'          => $post_id,
						':office_code' => $office_code,
						':browse_code' => $browse_code,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		return $rest_response;
	}
}
