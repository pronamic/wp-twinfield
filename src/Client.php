<?php
/**
 * Client
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WP/Twinfield
 */

namespace Pronamic\WP\Twinfield;

/**
 * Client
 *
 * This class connects to the Twinfield Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WP/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Client {
	/**
	 * The Twinfield WSDL login URL.
	 *
	 * @var string
	 */
	const WSDL_URL_LOGIN = 'https://login.twinfield.com/webservices/session.asmx?wsdl';

	/**
	 * Constructs and initializes an Twinfield client object.
	 */
	public function __construct() {
		// @see https://github.com/php-twinfield/twinfield/issues/50
		// @see https://github.com/php-twinfield/twinfield/pull/70/files
		$this->soap_client = new \SoapClient( self::WSDL_URL_LOGIN, Client::get_soap_client_options() );
	}

	/**
	 * Get SOAP Client options.
	 *
	 * @return array
	 */
	public static function get_soap_client_options() {
		return array(
			'classmap'           => self::get_class_map(),
			'connection_timeout' => 30,
			'trace'              => true,
			'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
			'cache_wsdl'         => WSDL_CACHE_MEMORY,
			'keep_alive'         => true,
		);
	}

	/**
	 * Get the class map to connect Twinfield classes to classes in this library.
	 *
	 * @return array
	 */
	public static function get_class_map() {
		return array(
			'ArrayOfArrayOfString'       => __NAMESPACE__ . '\ArrayOfArrayOfString',
			'ArrayOfMessageOfErrorCodes' => __NAMESPACE__ . '\ArrayOfMessageOfErrorCodes',
			'ArrayOfString'              => __NAMESPACE__ . '\ArrayOfString',
			'FinderData'                 => __NAMESPACE__ . '\FinderData',
			'LogonResponse'              => __NAMESPACE__ . '\LogonResponse',
			'MessageOfErrorCodes'        => __NAMESPACE__ . '\MessageOfErrorCodes',
			'ProcessXmlStringResponse'   => __NAMESPACE__ . '\ProcessXmlStringResponse',
			'SearchResponse'             => __NAMESPACE__ . '\SearchResponse',
			'SelectCompanyResponse'      => __NAMESPACE__ . '\SelectCompanyResponse',
		);
	}

	/**
	 * Find the session ID from the last Twinfield response message.
	 */
	private function get_session_id() {
		// Parse last response.
		$xml = $this->soap_client->__getLastResponse();

		$soap_envelope = simplexml_load_string( $xml, null, null, 'http://schemas.xmlsoap.org/soap/envelope/' );

		if ( false === $soap_envelope ) {
			return false;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- XML tag.

		if ( ! isset( $soap_envelope->Header ) ) {
			return false;
		}

		$soap_header = $soap_envelope->Header;

		$twinfield_header = $soap_header->children( 'http://www.twinfield.com/' )->Header;

		$session_id = (string) $twinfield_header->SessionID;

		// phpcs:enable

		return $session_id;
	}

	/**
	 * Logon with the specified credentials
	 *
	 * @param Credentials $credentials Logon with the specified credentials.
	 * @return LogonResponse
	 */
	public function logon( Credentials $credentials ) {
		$logon_response = $this->soap_client->Logon( $credentials );

		/*
		 * The session ID is officially not part of the logon response.
		 * To make this library easier to use we store it temporary in
		 * logon repsonse object.
		 */
		$logon_response->session_id = $this->get_session_id();

		return $logon_response;
	}

	/**
	 * Create an new session object from an logon response object.
	 *
	 * @param LogonResponse $logon_response An logon response is required to create a new session object.
	 * @return Session An Twinfield session object.
	 */
	public function get_session( LogonResponse $logon_response ) {
		// Check if logon response result code is OK.
		if ( LogonResult::OK !== $logon_response->get_result() ) {
			return false;
		}

		/*
		 * The session ID is officially not part of the logon response.
		 * To make this library easier to use we store it temporary in
		 * logon repsonse object.
		 */
		if ( empty( $logon_response->session_id ) ) {
			return false;
		}

		// OK.
		$session = new Session( $logon_response->session_id, $logon_response->get_cluster() );

		return $session;
	}
}
