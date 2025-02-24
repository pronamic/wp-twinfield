<?php
/**
 * Too Many Requests Exception
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Too Many Requests Exception class
 * 
 * @link https://docs.saloon.dev/installable-plugins/handling-rate-limits#handling-rate-limits-being-exceeded
 * @link https://www.php.net/manual/en/class.runtimeexception.php
 */
class TooManyRequestsException extends \RuntimeException {
	/**
	 * From SOAP fault.
	 * 
	 * @param \SoapFault $soap_fault SOAP fault.
	 * @return self
	 */
	public static function from_soap_client_fault( SoapClient $soap_client, \SoapFault $soap_fault ) {
		$message = \sprintf(
			'SOAP Fault Code: %s' . "\n" .
			'SOAP Fault Message: %s' . "\n" .
			'SOAP Fault JSON: %s' . "\n" .
			'SOAP Response Headers: %s',
			$soap_fault->getCode(),
			$soap_fault->getMessage(),
			\wp_json_encode( $soap_fault ),
			$soap_client->__getLastResponseHeaders()
		);

		$e = new self( $message, 429, $soap_fault );

		return $e;
	}
}
