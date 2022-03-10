<?php
/**
 * Sales invoice read request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\SalesInvoices;

use Pronamic\WordPress\Twinfield\ReadRequest;

/**
 * Sales invoice read request
 *
 * This class represents an Twinfield sales invoice read request.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SalesInvoiceReadRequest extends ReadRequest {
	/**
	 * Constructs and initialize an Twinfield article read request.
	 *
	 * @param string $office         Specify from wich office to read.
	 * @param string $code           Specifcy which sales invoice code to read.
	 * @param string $invoice_number The invoice number.
	 */
	public function __construct( $office, $code, $invoice_number ) {
		parent::__construct(
			[
				'type'          => 'salesinvoice',
				'office'        => $office,
				'code'          => $code,
				'invoicenumber' => $invoice_number,
			]
		);
	}
}
