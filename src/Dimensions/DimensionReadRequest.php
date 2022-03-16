<?php
/**
 * Dimension Read Request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Dimensions;

use Pronamic\WordPress\Twinfield\ReadRequest;

/**
 * Dimension Read Request
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DimensionReadRequest extends ReadRequest {
	/**
	 * Constructs and initializes an sales invoice service.
	 *
	 * @param string $office Office code.
	 * @param string $type  Dimension type.
	 * @param string $code  Dimension code.
	 */
	public function __construct( $office, $type, $code ) {
		parent::__construct(
			[
				'type'    => 'dimensions',
				'office'  => $office,
				'dimtype' => $type,
				'code'    => $code,
			]
		);
	}
}
