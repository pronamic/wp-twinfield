<?php
/**
 * SOAP client
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * SOAP client class
 */
class SoapClient extends \SoapClient {
	/**
	 * Construct SOAP client.
	 * 
	 * @link https://www.php.net/manual/en/soapclient.construct.php
	 * @param ?string $wsdl URI of a WSDL file describing the service, which is used to automatically configure the client. If not provided, the client will operate in non-WSDL mode.
	 * @param array   $options An associative array specifying additional options for the SOAP client. If `wsdl` is provided, this is optional; otherwise, at least location and url must be provided.
	 */
	public function __construct( ?string $wsdl, array $options = [] ) {
		try {
			return parent::__construct( $wsdl, $options );
		} catch ( \SoapFault $soap_fault ) {
			$this->handle_soap_fault( $soap_fault );
		}
	}

	/**
	 * Performs a SOAP request.
	 * 
	 * @link https://www.php.net/manual/en/soapclient.dorequest.php
	 * @param string $request  The XML SOAP request.
	 * @param string $location The URL to request.
	 * @param string $action   The SOAP action.
	 * @param int    $version  The SOAP version.
	 * @param bool   $one_way  If `one_way` is set to `true`, this method returns nothing. Use this where a response is not expected.
	 * @return The XML SOAP response.
	 */
	public function __doRequest( string $request, string $location, string $action, int $version, bool $one_way = false ): ?string {
		try {
			return parent::__doRequest( $request, $location, $action, $version, $one_way );
		} catch ( \SoapFault $soap_fault ) {
			$this->handle_soap_fault( $soap_fault );
		}
	}

	/**
	 * Handles a SOAP fault exception.
	 *
	 * @param \SoapFault $soap_fault The SOAP fault exception.
	 * @throws TooManyRequestsException If the error is due to rate limiting.
	 * @throws \SoapFault If another SOAP error occurs.
	 */
	public function handle_soap_fault( \SoapFault $soap_fault ): void {
		if ( $this->is_too_many_requests( $soap_fault ) ) {
			throw TooManyRequestsException::from_soap_client_fault( $this, $soap_fault );
		}

		throw $soap_fault;
	}

	/**
	 * Is too many requests.
	 * 
	 * @param \SoapFault $e SOAP fault exception.
	 * @return bool
	 */
	private function is_too_many_requests( \SoapFault $soap_fault ): bool {
		return \str_contains( $soap_fault->getMessage(), 'Too Many Requests' );
	}
}
