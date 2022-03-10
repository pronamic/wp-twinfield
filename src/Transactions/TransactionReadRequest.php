<?php
/**
 * Transaction read request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use Pronamic\WordPress\Twinfield\ReadRequest;

/**
 * Transaction read request
 *
 * This class represents an Twinfield sales invoice read request.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class TransactionReadRequest extends ReadRequest {
	/**
	 * Constructs and initialize an Twinfield transaction read request.
	 *
	 * @param string $office Specify from wich office to read.
	 * @param string $code   Specifcy which transaction code to read.
	 * @param string $number The transaction number.
	 */
	public function __construct( $office, $code, $number ) {
		parent::__construct(
			[
				'type'   => 'transaction',
				'office' => $office,
				'code'   => $code,
				'number' => $number,
			]
		);
	}
}
