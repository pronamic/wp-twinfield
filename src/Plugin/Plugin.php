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
use Pronamic\WordPress\Twinfield\Dimensions\Dimension;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Transactions\TransactionType;
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
	 * Instance.
	 *
	 * @var self|null
	 */
	protected static $instance;

	/**
	 * Controllers.
	 * 
	 * @var array
	 */
	private $controllers = [];

	/**
	 * Instance.
	 *
	 * @param string|null $file Plugin file.
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct plugin.
	 */
	public function __construct() {
		$this->controllers[] = new RestApi( $this );

		if ( is_admin() ) {
			$this->controllers[] = new Admin( $this );
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$this->controllers[] = new CLI();
		}

		$this->controllers[] = new AuthorizationPostType( $this );
		$this->controllers[] = new CustomerPostTypeSupport();
		$this->controllers[] = new ArticlePostTypeSupport();
		$this->controllers[] = new SaveBankStatementController( $this );
		$this->controllers[] = new SaveOfficeController( $this );
		$this->controllers[] = new SaveHierarchyController( $this );
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		foreach ( $this->controllers as $controller ) {
			if ( \method_exists( $controller, 'setup' ) ) {
				$controller->setup();
			}
		}

		\add_action( 'init', $this->init( ... ), 9 );
		\add_filter( 'query_vars', $this->query_vars( ... ) );
		\add_filter( 'template_include', $this->template_include( ... ) );

		\add_filter(
			'pronamic_twinfield_client',
			fn() => $this->get_client( \get_post( \get_option( 'pronamic_twinfield_authorization_post_id' ) ) )
		);

		\add_filter(
			'redirect_canonical',
			function ( $redirect_url, $requested_url ) {
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

		// Tables.
		$this->install_tables();

		// Authorize.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->maybe_handle_authorize( $_GET );
	}

	/**
	 * Install tables.
	 *
	 * @return void
	 */
	private function install_tables() {
		global $wpdb;

		$version = '1.0.2';

		$db_version = \get_option( 'pronamic_twinfield_db_version' );

		if ( $version === $db_version ) {
			return;
		}

		$queries = "
			CREATE TABLE {$wpdb->prefix}twinfield_organisations (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				code VARCHAR(80) NOT NULL,
				PRIMARY KEY  ( id ),
				UNIQUE KEY code ( code )
			);

			CREATE TABLE {$wpdb->prefix}twinfield_offices (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				organisation_id BIGINT UNSIGNED NOT NULL,
				code VARCHAR(80) NOT NULL,
				name VARCHAR(80) DEFAULT NULL,
				shortname VARCHAR(20) DEFAULT NULL,
				is_demo TINYINT(1) DEFAULT NULL,
				is_template TINYINT(1) DEFAULT NULL,
				xml LONGTEXT NOT NULL,
				PRIMARY KEY  ( id ),
				KEY organisation_id ( organisation_id ),
				UNIQUE KEY code ( organisation_id, code )
			);

			CREATE TABLE {$wpdb->prefix}twinfield_bank_statements (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				office_id BIGINT UNSIGNED NOT NULL,
				code VARCHAR(80) NOT NULL,
				`number` INT UNSIGNED NOT NULL,
				sub_id INT UNSIGNED NOT NULL,
				account_number VARCHAR(40) NOT NULL,
				iban VARCHAR(40) NOT NULL,
				`date` DATE NOT NULL,
				currency VARCHAR(3) NOT NULL,
				opening_balance DECIMAL(15,2) NOT NULL,
				closing_balance DECIMAL(15,2) NOT NULL,
				transaction_number VARCHAR(16),
				PRIMARY KEY  (id),
				KEY office_id ( office_id ),
				KEY code ( office_id, code ),
				KEY code_number ( office_id, code, `number` ),
				UNIQUE KEY bank_statement ( office_id, code, `number`, sub_id ),
				KEY transaction_number ( office_id, code, transaction_number ),
				KEY `date` ( `date` )
			);

			CREATE TABLE {$wpdb->prefix}twinfield_bank_statement_lines (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				bank_statement_id BIGINT UNSIGNED NOT NULL,
				line_id INT UNSIGNED NOT NULL,
				contra_account_number VARCHAR(40) NOT NULL,
				contra_iban VARCHAR(40) NOT NULL,
				contra_account_name TINYTEXT NOT NULL,
				payment_reference VARCHAR(80) NOT NULL,
				amount DECIMAL(15,2) NOT NULL,
				base_amount DECIMAL(15,2) NOT NULL,
				description TEXT NOT NULL,
				transaction_type_id VARCHAR(16) NOT NULL,
				reference VARCHAR(80) NOT NULL,
				end_to_end_id VARCHAR(80) NOT NULL,
				return_reason VARCHAR(80) NOT NULL,
				PRIMARY KEY  (id),
				KEY bank_statement_id ( bank_statement_id ),
				UNIQUE KEY bank_statement_line ( bank_statement_id, line_id )
			);

			CREATE TABLE {$wpdb->prefix}twinfield_hierarchies (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				created_at DATETIME NOT NULL,
				updated_at DATETIME NOT NULL,
				office_id BIGINT UNSIGNED NOT NULL,
				code VARCHAR(80) NOT NULL,
				json LONGTEXT NOT NULL,
				PRIMARY KEY  ( id ),
				KEY office_id ( office_id ),
				UNIQUE KEY code ( office_id, code )
			);
		";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $queries );

		$wpdb->query(
			"
			ALTER TABLE {$wpdb->prefix}twinfield_offices
			ADD CONSTRAINT fk_office_organisation_id
			FOREIGN KEY ( organisation_id )
			REFERENCES {$wpdb->prefix}twinfield_organisations ( id )
			ON DELETE RESTRICT
			ON UPDATE RESTRICT
			;
		" 
		);

		$wpdb->query(
			"
			ALTER TABLE {$wpdb->prefix}twinfield_bank_statements
			ADD CONSTRAINT fk_bank_statement_office_id
			FOREIGN KEY ( office_id )
			REFERENCES {$wpdb->prefix}twinfield_offices ( id )
			ON DELETE RESTRICT
			ON UPDATE RESTRICT
			;
		" 
		);

		$wpdb->query(
			"
			ALTER TABLE {$wpdb->prefix}twinfield_bank_statement_lines
			ADD CONSTRAINT fk_bank_statement_line_bank_stament_id
			FOREIGN KEY ( bank_statement_id )
			REFERENCES {$wpdb->prefix}twinfield_bank_statements ( id )
			ON DELETE RESTRICT
			ON UPDATE RESTRICT
			;
		" 
		);

		$wpdb->query(
			"
			ALTER TABLE {$wpdb->prefix}twinfield_hierarchies
			ADD CONSTRAINT fk_hierarchy_office_id
			FOREIGN KEY ( office_id )
			REFERENCES {$wpdb->prefix}twinfield_offices ( id )
			ON DELETE RESTRICT
			ON UPDATE RESTRICT
			;
		" 
		);

		\update_option( 'pronamic_twinfield_db_version', $version );
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

		$state_decoded = \base64_decode( (string) $data['state'], true );

		if ( false === $state_decoded ) {
			return;
		}

		$state_object = \json_decode( $state_decoded );

		if ( ! is_object( $state_object ) ) {
			return;
		}

		if ( ! property_exists( $state_object, 'plugin' ) ) {
			return;
		}

		if ( ! property_exists( $state_object, 'post_id' ) ) {
			return;
		}

		if ( 'pronamic-twinfield' !== $state_object->plugin ) {
			return;
		}

		$url = \add_query_arg(
			[
				'code' => $data['code'],
			],
			\rest_url( 'pronamic-twinfield/v1/authorize/' . $state_object->post_id )
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
	 * Get link for object.
	 *
	 * @param int    $post_id Post ID.
	 * @param object $entity  Object.
	 * @return string
	 */
	public function get_link( $post_id, $entity ) {
		if ( $entity instanceof Office ) {
			return \home_url( 'pronamic-twinfield/authorizations/' . $post_id . '/offices/' . $entity->get_code() );
		}

		if ( $entity instanceof TransactionType ) {
			$office = $entity->get_office();

			return \home_url( 'pronamic-twinfield/authorizations/' . $post_id . '/offices/' . $office->get_code() . '/transaction-types/' . $entity->get_code() );
		}

		if ( $entity instanceof Dimension ) {
			return \home_url(
				\strtr(
					'pronamic-twinfield/v1/authorizations/:id/dimensions/:office_code/:dimension_type_code/:dimension_code',
					[
						':id'                  => $post_id,
						':office_code'         => $entity->get_type()->get_office()->get_code(),
						':dimension_type_code' => $entity->get_type()->get_code(),
						':dimension_code'      => $entity->get_code(),
					]
				)
			);
		}
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
						case 'dimension':
							$dimension = $data->data;

							$template = 'dimension.php';

							if ( 'DEB' === $dimension->get_type() ) {
								$template = 'customer.php';
							}

							include __DIR__ . '/../../templates/' . $template;

							return false;
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
							$post_id     = $data->post_id;

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
					$authentication->get_validation()->organisation->get_code(),
					$authentication->get_validation()->user->get_code(),
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

		$authentication = AuthenticationInfo::from_object( \json_decode( (string) $post->post_content ) );

		$client = new Client( $openid_connect_client, $authentication );

		$client->set_authentication_refresh_handler(
			function ( $client ) use ( $post ): void {
				$this->save_authentication( $post, $client->get_authentication() );
			}
		);

		return $client;
	}

	/**
	 * Get entity manager.
	 * 
	 * @return EntityManager
	 */
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
					'xml'             => '%s',
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
					'date'               => '%s',
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

		$orm->register_entity(
			\Pronamic\WordPress\Twinfield\Hierarchies\Hierarchy::class,
			new Entity(
				$wpdb->prefix . 'twinfield_hierarchies',
				'id',
				[
					'office_id'   => '%d',
					'code'        => '%s',
					'name'        => '%s',
					'description' => '%s',
					'json'        => '%s',
				]
			)
		);

		return $orm;
	}
}
