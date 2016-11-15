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
 * Login client
 *
 * This class connects to the Twinfield Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WP/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class LoginClient {
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
		$this->soap_client = new \SoapClient( self::WSDL_URL_LOGIN, array(
			'classmap' => Client::get_class_map(),
			'trace'    => 1,
		) );
	}

	/**
	 * Find the session ID from the last Twinfield response message.
	 */
	private function get_session_id() {
		// Parse last response.
		$xml = $this->soap_client->__getLastResponse();

		$soap_envelope    = simplexml_load_string( $xml, null, null, 'http://schemas.xmlsoap.org/soap/envelope/' );
		$soap_header      = $soap_envelope->Header;
		$twinfield_header = $soap_header->children( 'http://www.twinfield.com/' )->Header;

		// Session ID.
		$session_id = (string) $twinfield_header->SessionID;

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
		$session = null;

		// Check if logon response result code is OK.
		if ( LogonResult::OK === $logon_response->get_result() ) {
			/*
			 * The session ID is officially not part of the logon response.
			 * To make this library easier to use we store it temporary in
			 * logon repsonse object.
			 */
			$session_id = $logon_response->session_id;

			$session = new Session( $session_id, $logon_response->get_cluster() );
		}

		return $session;
	}
}