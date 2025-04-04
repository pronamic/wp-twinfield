<?php
/**
 * Client
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use Pronamic\WordPress\Twinfield\Authentication\AccessTokenValidation;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Authentication\AuthenticationTokens;
use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;
use Pronamic\WordPress\Twinfield\Authentication\InvalidTokenException;
use Pronamic\WordPress\Twinfield\Finder\Search;
use Pronamic\WordPress\Twinfield\Offices\OfficeService;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Finder\Finder;

/**
 * Client
 *
 * This class connects to the Twinfield Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Client {
	/**
	 * OpenID connect client.
	 * 
	 * @var OpenIdConnectClient
	 */
	private $openid_connect_client;

	/**
	 * Services.
	 *
	 * @var array
	 */
	private $services;

	/**
	 * Authentication refresh handler
	 * 
	 * @var callable|null
	 */
	private $authentication_refresh_handler;

	/**
	 * Cluster URL.
	 * 
	 * @var string
	 */
	private $cluster_url;

	/**
	 * Constructs and initializes a Twinfield client object.
	 *
	 * @param OpenIdConnectClient $openid_connect_client OpenID Connect Client.
	 * @param AuthenticationInfo  $authentication        Authentication info.
	 */
	public function __construct( OpenIdConnectClient $openid_connect_client, AuthenticationInfo $authentication ) {
		$this->openid_connect_client = $openid_connect_client;

		$this->set_authentication( $authentication );

		$this->services = [];
	}

	/**
	 * Get authentication.
	 * 
	 * @return AuthenticationInfo
	 */
	public function get_authentication() {
		return $this->authentication;
	}

	/**
	 * Set authentication.
	 * 
	 * @param AuthenticationInfo $authentication Authentication.
	 * @return void
	 */
	public function set_authentication( AuthenticationInfo $authentication ) {
		$this->authentication = $authentication;

		$validation = $this->authentication->get_validation();

		$this->organisation = $validation->organisation;
		$this->user         = $validation->user;
		$this->cluster_url  = $validation->cluster_url;
	}

	/**
	 * Set authentication refresh handler.
	 * 
	 * @param callback $callback Callback.
	 * @return void
	 */
	public function set_authentication_refresh_handler( $callback ) {
		$this->authentication_refresh_handler = $callback;
	}

	/**
	 * Authenticate.
	 * 
	 * @return AuthenticationInfo
	 */
	public function authenticate() {
		if ( $this->authentication->get_validation()->expires_in( 5 * \MINUTE_IN_SECONDS ) ) {
			$response = $this->openid_connect_client->refresh_token( $this->authentication->get_tokens()->get_refresh_token() );
			
			$tokens = AuthenticationTokens::from_object( $response );

			$response = $this->openid_connect_client->get_access_token_validation( $tokens->get_access_token() );

			$validation = AccessTokenValidation::from_object( $response );

			$authentication = new AuthenticationInfo( $tokens, $validation );

			$this->set_authentication( $authentication );

			if ( \is_callable( $this->authentication_refresh_handler ) ) {
				\call_user_func( $this->authentication_refresh_handler, $this );
			}
		}

		return $this->authentication;
	}

	/**
	 * Get service by name.
	 *
	 * @param string $name Name.
	 * @return mixed
	 */
	public function get_service( $name ) {
		if ( isset( $this->services[ $name ] ) ) {
			return $this->services[ $name ];
		}

		$service = $this->new_service( $name );

		if ( $service ) {
			$this->set_service( $name, $service );
		}

		return $service;
	}

	/**
	 * Generate new service by name.
	 *
	 * @param string $name Name.
	 * @return mixed
	 */
	private function new_service( $name ) {
		switch ( $name ) {
			case 'declarations':
				return new Declarations\DeclarationsService( $this );
			case 'deleted-transactions':
				return new Transactions\DeletedTransactionsService( $this );
			case 'document':
				return new Documents\DocumentService( $this );
			case 'finder':
				return new Finder( $this );
			case 'office':
				return new Offices\OfficeService( $this );
			case 'processxml':
				return new XMLProcessor( $this );
			case 'periods':
				return new Periods\PeriodsService( $this );
			case 'hierarchies':
				return new Hierarchies\HierarchyService( $this );
			case 'budget':
				return new Budget\BudgetService( $this );
			default:
				return false;
		}
	}

	/**
	 * Set service.
	 *
	 * @param string $name    Name.
	 * @param mixed  $service Service.
	 */
	private function set_service( $name, $service ) {
		$this->services[ $name ] = $service;
	}

	/**
	 * Get finder.
	 *
	 * @return Finder
	 */
	public function get_finder() {
		return $this->get_service( 'finder' );
	}

	/**
	 * Get XML processor.
	 *
	 * @return XMLProcessor
	 */
	public function get_xml_processor() {
		return $this->get_service( 'processxml' );
	}

	/**
	 * Get organisation.
	 * 
	 * @return Organisation
	 */
	public function get_organisation() {
		return $this->organisation;
	}

	/**
	 * Get user.
	 * 
	 * @return User
	 */
	public function get_user() {
		return $this->user;
	}

	/**
	 * Get offices.
	 * 
	 * @return Office[]
	 */
	public function get_offices() {
		$office_service = new OfficeService( $this );

		return $office_service->get_offices();
	}

	/**
	 * Get office.
	 * 
	 * @param Office $office Office.
	 * @return Office
	 */
	public function get_office( Office $office ) {
		$office_service = new OfficeService( $this );

		return $office_service->get_office( $office );
	}

	/**
	 * Get transaction types.
	 * 
	 * @param Office $office Office.
	 * @return array
	 */
	public function get_transaction_types( Office $office ) {
		$finder = $this->get_finder();

		// Request.
		$search = new Search(
			'TRS',
			'*',
			0,
			1,
			100,
			[
				'hidden' => '1',
			]
		);

		$finder->set_office( $office );

		$response = $finder->search( $search );

		$data = $response->get_data();

		$items = $data->get_items();

		$transaction_types = [];

		foreach ( $items as $item ) {
			$transaction_type = $office->new_transaction_type( $item[0] );

			$transaction_type->set_name( $item[1] );

			$transaction_types[] = $transaction_type;
		}

		return $transaction_types;
	}

	/**
	 * Get WSDL URL.
	 * 
	 * @param string $wsdl_file WSDL file.
	 * @return string
	 */
	private function get_wsdl_url( $wsdl_file ) {
		return $this->cluster_url . $wsdl_file;
	}

	/**
	 * New SOAP client.
	 * 
	 * @param string $wsdl_file WSDL file.
	 * @return SoapClient
	 */
	public function new_soap_client( $wsdl_file ) {
		return new SoapClient( $this->get_wsdl_url( $wsdl_file ), $this->get_soap_client_options() );
	}

	/**
	 * Get SOAP Client options.
	 *
	 * @return array
	 */
	private function get_soap_client_options() {
		return [
			'connection_timeout' => 30,
			'compression'        => \SOAP_COMPRESSION_ACCEPT | \SOAP_COMPRESSION_GZIP,
			/**
			 * The `trace` option must be set to `true` to use the `SoapClient::__getLastResponseHeaders` function to
			 * retrieve the rate limiting headers for 'Too Many Requests' errors.
			 * 
			 * @link https://www.php.net/manual/en/soapclient.getlastresponseheaders.php
			 */
			'trace'              => true,
			/**
			 * In the linked issue `WSDL_CACHE_MEMORY` was recommended, but `WSDL_CACHE_BOTH` may be the better option in newer PHP versions.
			 * 
			 * @link https://github.com/php-twinfield/twinfield/issues/50
			 */
			'cache_wsdl'         => \WSDL_CACHE_BOTH,
			/**
			 * The `keep_alive` is set to `false` to prevent 'error fetching HTTP headers' and 'Could not connect to host' errors.
			 *
			 * @link https://github.com/php-twinfield/twinfield/issues/50
			 */
			'keep_alive'         => false,
		];
	}
}
