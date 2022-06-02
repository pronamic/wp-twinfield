<?php
/**
 * REST API
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Customers\Customer;
use Pronamic\WordPress\Twinfield\Suppliers\Supplier;
use Pronamic\WordPress\Twinfield\Dimensions\Dimension;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationTokens;
use Pronamic\WordPress\Twinfield\Authentication\AccessTokenValidation;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Budget\BudgetByProfitAndLossQuery;
use Pronamic\WordPress\Twinfield\Offices\OfficeReadRequest;
use Pronamic\WordPress\Twinfield\Offices\OfficesXmlReader;
use Pronamic\WordPress\Twinfield\Offices\OfficesListRequest;
use Pronamic\WordPress\Twinfield\Finder\Search;
use Pronamic\WordPress\Twinfield\Twinfield;
use Pronamic\WordPress\Twinfield\Transactions\DeletedTransactionsQuery;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

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
	 * 
	 * @param Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );

		\add_filter( 'rest_post_dispatch', [ $this, 'rest_post_dispatch' ], 10, 3 );
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
				'dimtype' => 'AST',
			],
			[
				'slug'    => 'projects',
				'type'    => 'DIM',
				'dimtype' => 'PRJ',
			],
			[
				'slug'    => 'activities',
				'type'    => 'DIM',
				'dimtype' => 'ACT',
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
						$post = get_post( $request->get_param( 'post_id' ) );

						$client = $this->plugin->get_client( $post );

						$organisation = $client->get_organisation();

						$office_code = $request->get_param( 'office_code' );

						$office = $organisation->office( $office_code );

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
											$supplier->set_office( $office );

											$data[] = $this->add_links_to_collection_item( 'pronamic-twinfield/v1/authorizations/' . $post->ID, $supplier );
										}

										return $data;
									case 'DEB':
										$data = array();

										foreach ( $response->get_data() as $item ) {
											$customer = new Customer( $dimtype, $item[0] );
											$customer->set_name( $item[1] );
											$customer->set_office( $office );

											$data[] = $this->add_links_to_collection_item( 'pronamic-twinfield/v1/authorizations/' . $post->ID, $customer );
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
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/budget/(?P<budget_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_budget' ],
				'permission_callback' => function () {
					return true;
				},
				'args'                => [
					'post_id'             => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'required'          => true,
					],
					'office_code'         => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
						'required'    => true,
					],
					'budget_code'         => [
						'description' => 'Twinfield budget code.',
						'type'        => 'string',
						'required'    => true,
						'default'     => '001',
					],
					'year'                => [
						'description' => 'Year to be retrieved.',
						'type'        => 'int',
						'required'    => true,
						'default'     => \wp_date( 'Y' ),
					],
					'include_provisional' => [
						'description' => 'Include provisional transactions.',
						'type'        => 'bool',
						'required'    => true,
						'default'     => true,
					],
					'include_final'       => [
						'description' => 'Include final transactions.',
						'type'        => 'bool',
						'required'    => true,
						'default'     => true,
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

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/bank-statements',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_bank_statements' ],
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
					'date_from'      => [
						'description' => 'All statements with a statement date equal to or higher than this value will be included.',
						'type'        => 'string',
						'default'     => 'yesterday',
					],
					'date_to'        => [
						'description' => 'All statements with a statement date equal to or lower than this value will be included.',
						'type'        => 'string',
						'default'     => 'midnight',
					],
					'include_posted' => [
						'description' => 'If value is true, statements that have been posted will be included.',
						'type'        => 'boolean',
						'default'     => true,
					],
				],
			]
		);
	}

	/**
	 * Add links to collection item.
	 * 
	 * @param string $prefix Prefix.
	 * @param mixed  $item   Item.
	 * @return array
	 */
	private function add_links_to_collection_item( $prefix, $item ) {
		if ( $item instanceof Dimension ) {
			$data = (array) $item->jsonSerialize();

			$data['_links'] = [
				'dimension' => [
					'href' => rest_url(
						strtr(
							$prefix . '/dimensions/:office_code/:dimension_type/:dimension_code',
							[
								':office_code'    => $item->get_office()->get_code(),
								':dimension_type' => $item->get_type(),
								':dimension_code' => $item->get_code(),
							]
						)
					),
				],
			];

			return $data;
		}

		return $item;
	}

	/**
	 * Redirect authorizations.
	 * 
	 * @param string $route Route.
	 * @return WP_REST_Response
	 */
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

	/**
	 * REST API authorize.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
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

	/**
	 * REST API organisation.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
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

	/**
	 * REST API offices.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_offices( WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );

		$client = $this->plugin->get_client( $post );

		$xml_processor = $client->get_xml_processor();

		$offices_list_request = new OfficesListRequest();

		$offices_list_response = $xml_processor->process_xml_string( $offices_list_request->to_xml() );

		$offices_xml_reader = new OfficesXmlReader( $client->get_organisation() );

		$offices = $offices_xml_reader->read( $offices_list_response );

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

	/**
	 * REST API finder types.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
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

	/**
	 * REST API finder.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
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
			$office = $organisation->office( $office_code );

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

	/**
	 * REST API office.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_office( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$request = new OfficeReadRequest( $office_code );

		$response = $xml_processor->process_xml_string( $request->to_xml() );

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

	/**
	 * REST API hierarchy.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_hierarchy( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$hierarchies_service = $client->get_service( 'hierarchies' );

		$hierarchies_service->set_office( $office );

		$hierarchy_code = $request->get_param( 'hierarchy_code' );

		$hierarchy = $hierarchies_service->get_hierarchy( $hierarchy_code );

		return $hierarchy;
	}

	/**
	 * REST API budget.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_budget( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$budget_service = $client->get_service( 'budget' );

		$budget_service->set_office( $office );

		$code                = $request->get_param( 'budget_code' );
		$year                = $request->get_param( 'year' );
		$period_from         = null;
		$period_to           = null;
		$include_provisional = $request->get_param( 'include_provisional' );
		$include_final       = $request->get_param( 'include_final' );

		$query = new BudgetByProfitAndLossQuery( $code, $year, $period_from, $period_to, $include_provisional, $include_final );

		$budget = $budget_service->get_budget_by_profit_and_loss_query( $office, $query );

		return $budget;
	}

	/**
	 * REST API deleted transactions.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_deleted_transactions( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$deleted_transactions_service = $client->get_service( 'deleted-transactions' );

		$deleted_transactions_service->set_office( $office );

		$deleted_transactions_query = new DeletedTransactionsQuery( $office_code );

		$deleted_transactions = $deleted_transactions_service->get_deleted_transactions( $deleted_transactions_query );

		return $deleted_transactions;
	}

	/**
	 * REST API years.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_years( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

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

	/**
	 * REST API periods.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_periods( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$periods_service = $client->get_service( 'periods' );

		$periods_service->set_office( $office );

		$year = $request->get_param( 'year' );

		$periods = $periods_service->get_periods( $office, $year );

		return $periods;
	}

	/**
	 * REST API declarations.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_declarations( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$declarations_service = $client->get_service( 'declarations' );

		$summaries = $declarations_service->get_all_summaries( $office );

		return $summaries;
	}

	/**
	 * REST API transaction.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_transaction( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$request = new \Pronamic\WordPress\Twinfield\Transactions\TransactionReadRequest(
			$request->get_param( 'office_code' ),
			$request->get_param( 'transaction_type_code' ),
			$request->get_param( 'transaction_number' )
		);

		$response = $xml_processor->process_xml_string( $request->to_xml() );

		$transaction_unserializer = new \Pronamic\WordPress\Twinfield\Transactions\TransactionUnserializer( $organisation );

		$transaction = $transaction_unserializer->unserialize_string( (string) $response );

		$data = [
			'type'      => 'transaction',
			'data'      => $transaction,
			'post_id'   => $post_id,
			'_embedded' => (object) [
				'request'  => $request->to_xml(),
				'response' => (string) $response,
			],
		];

		$response = new \WP_REST_Response( $data );

		$response->add_link(
			'authorization',
			\rest_url(
				\strtr(
					'pronamic-twinfield/v1/authorizations/:id',
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

	/**
	 * REST API sales invoice.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_sales_invoice( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$xml_processor = $client->get_xml_processor();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$read_request = new \Pronamic\WordPress\Twinfield\SalesInvoices\SalesInvoiceReadRequest(
			$request->get_param( 'office_code' ),
			$request->get_param( 'invoice_type_code' ),
			$request->get_param( 'invoice_number' )
		);

		$read_response = $xml_processor->process_xml_string( $read_request->to_xml() );

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
	 * REST API dimension.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_dimension( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$xml_processor = $client->get_xml_processor();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$dimension_read_request = new \Pronamic\WordPress\Twinfield\Dimensions\DimensionReadRequest(
			$request->get_param( 'office_code' ),
			$request->get_param( 'dimension_type_code' ),
			$request->get_param( 'dimension_code' )
		);

		$dimension_read_response = $xml_processor->process_xml_string( $dimension_read_request->to_xml() );

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

	/**
	 * REST API customers list.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
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

	/**
	 * REST API customers.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
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

	/**
	 * REST API articles.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
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

	/**
	 * REST API browse fields.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_browse_fields( WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$document = new \DOMDocument();

		$browse_code = $request->get_param( 'browse_code' );

		$read_element = $document->appendChild( $document->createElement( 'read' ) );

		$read_element->appendChild( $document->createElement( 'type', 'browse' ) );
		$read_element->appendChild( $document->createElement( 'office', $office_code ) );
		$read_element->appendChild( $document->createElement( 'code', $browse_code ) );

		$xml = $document->saveXML( $document->documentElement );

		$response = $xml_processor->process_xml_string( $xml );

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

	/**
	 * REST API browse query.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_browse_query( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

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
				 * Syntax for values between a range.
				 *
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

		$response = $xml_processor->process_xml_string( $xml );

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

	/**
	 * REST API bank statements.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_bank_statements( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$bank_statements_service = new \Pronamic\WordPress\Twinfield\BankStatements\BankStatementsService( $client );

		$query = new \Pronamic\WordPress\Twinfield\BankStatements\BankStatementsQuery(
			new \DateTimeImmutable( $request->get_param( 'date_from' ) ),
			new \DateTimeImmutable( $request->get_param( 'date_to' ) ),
			(bool) $request->get_param( 'include_posted' )
		);

		$bank_statements = $bank_statements_service->get_bank_statements( $office, $query );

		return $bank_statements;
	}

	/**
	 * REST API post dispatch.
	 * 
	 * @param WP_HTTP_Response $result  Result to send to the client. Usually a `WP_REST_Response`.
	 * @param WP_REST_Server   $server  Server instance.
	 * @param WP_REST_Request  $request Request used to generate the response.
	 * @return WP_HTTP_Response
	 */
	public function rest_post_dispatch( WP_HTTP_Response $response, WP_REST_Server $server, WP_REST_Request $request ) {
		$data = $response->get_data();

		if ( ! $request->has_param( 'pull' ) ) {
			return $response;
		}

		if ( $data instanceof \Pronamic\WordPress\Twinfield\BankStatements\BankStatements ) {
			header( 'Content-Type: text/html' );

			var_dump( $data );

			$this->bank_statements_update_or_create( $data );
		}

		return $response;
	}

	/**
	 * Upsert.
	 * 
	 * @link https://atymic.dev/tips/laravel-8-upserts/
	 * @link https://laravel.com/docs/9.x/eloquent#upserts
	 * @link https://stackoverflow.com/questions/2634152/getting-mysql-insert-id-while-using-on-duplicate-key-update-with-php
	 */
	public function bank_statements_update_or_create( $bank_statements ) {
		$orm = $this->get_orm();

		$office = $bank_statements->get_office();

		$organisation = $office->get_organisation();

		$organisation_id = $orm->first_or_create(
			$organisation,
			[
				'code' => $organisation->get_code(),
			],
			[],
		);

		$office_id = $orm->first_or_create(
			$office,
			[
				'organisation_id' => $organisation_id,
				'code'            => $office->get_code(),
			],
			[]
		);

		foreach ( $bank_statements as $bank_statement ) {
			$data = $bank_statement->jsonSerialize();

			$bank_statement_id = $orm->update_or_create(
				$bank_statement,
				[
					'office_id' => $office_id,
					'code'      => $data->code,
					'number'    => $data->number,
					'sub_id'    => $data->sub_id,
				],
				[
					'account_number'     => $data->account_number,
					'iban'               => $data->iban,
					'statement_date'     => $bank_statement->get_date()->format( 'Y-m-d H:i:s' ),
					'currency'           => $data->currency,
					'opening_balance'    => $data->opening_balance,
					'closing_balance'    => $data->closing_balance,
					'transaction_number' => $data->transaction_number,
				],
				true
			);

			foreach ( $bank_statement->get_lines() as $line ) {
				$data = $line->jsonSerialize();

				$bank_statement_line_id = $orm->update_or_create(
					$line,
					[
						'bank_statement_id' => $bank_statement_id,
						'line_id'           => $line->get_id(),
					],
					[
						'contra_account_number' => $data->contra_account_number,
						'contra_iban'           => $data->contra_iban,
						'contra_account_name'   => $data->contra_account_name,
						'payment_reference'     => $data->payment_reference,
						'amount'                => $data->amount,
						'base_amount'           => $data->base_amount,
						'description'           => $data->description,
						'transaction_type_id'   => $data->transaction_type_id,
						'reference'             => $data->reference,
						'end_to_end_id'         => $data->end_to_end_id,
						'return_reason'         => $data->return_reason,
					],
					true
				);
			}
		}
	}

	public function get_orm() {
		global $wpdb;

		$orm = new EntityManager( $wpdb );

		$orm->register_entity(
			\Pronamic\WordPress\Twinfield\Organisations\Organisation::class,
			new Entity(
				$wpdb->prefix . 'twinfield_organisations',
				'id',
				[
					'code' => '%s',
				]
			)
		);

		$orm->register_entity(
			\Pronamic\WordPress\Twinfield\Offices\Office::class,
			new Entity(
				$wpdb->prefix . 'twinfield_offices',
				'id',
				[
					'organisation_id' => '%d',
					'code'            => '%s',
				]
			)
		);

		$orm->register_entity(
			\Pronamic\WordPress\Twinfield\BankStatements\BankStatement::class,
			new Entity(
				$wpdb->prefix . 'twinfield_bank_statements',
				'id',
				[
					'office_id'          => '%d',
					'code'               => '%s',
					'number'             => '%d',
					'sub_id'             => '%d',
					'account_number'     => '%s',
					'iban'               => '%s',
					'statement_date'     => '%s',
					'currency'           => '%s',
					'opening_balance'    => '%f',
					'closing_balance'    => '%f',
					'transaction_number' => '%s',
				]
			)
		);

		$orm->register_entity(
			\Pronamic\WordPress\Twinfield\BankStatements\BankStatementLine::class,
			new Entity(
				$wpdb->prefix . 'twinfield_bank_statement_lines',
				'id',
				[
					'bank_statement_id'     => '%d',
					'line_id'               => '%d',
					'contra_account_number' => '%s',
					'contra_iban'           => '%s',
					'contra_account_name'   => '%s',
					'payment_reference'     => '%s',
					'amount'                => '%s',
					'base_amount'           => '%f',
					'description'           => '%s',
					'transaction_type_id'   => '%s',
					'reference'             => '%s',
					'end_to_end_id'         => '%s',
					'return_reason'         => '%s',
				]
			)
		);

		return $orm;
	}
}
