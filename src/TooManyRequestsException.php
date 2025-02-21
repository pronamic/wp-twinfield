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
 */
class TooManyRequestsException extends \RuntimeException {
	/**
	 * From SOAP fault.
	 * 
	 * @param \SoapFault $soap_fault SOAP fault.
	 * @return self
	 */
	public function from_soap_client_fault( SoapClient $soap_client, \SoapFault $soap_fault ) {
		$message = \sprintf(
			'SOAP Fault: %s' . "\n" .
			'SOAP Response Headers: %s',
			$soap_fault->getMessage(),
			$soap_client->__getLastResponseHeaders()
		);

		$e = new self( $message, 429, $soap_fault );

		return $e;
	}
}
