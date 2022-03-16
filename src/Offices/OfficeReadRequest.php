<?php
/**
 * Office Read Request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use Pronamic\WordPress\Twinfield\ReadRequest;

/**
 * Office Read Request
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class OfficeReadRequest extends ReadRequest {
	/**
	 * Construct office read request.
	 *
	 * @param string $code Office code.
	 */
	public function __construct( $code ) {
		parent::__construct(
			[
				'type' => 'office',
				'code' => $code,
			]
		);
	}
}
