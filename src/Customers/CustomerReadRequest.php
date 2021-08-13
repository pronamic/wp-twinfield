<?php
/**
 * Customer read request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Customers;

use Pronamic\WordPress\Twinfield\ReadRequest;

/**
 * Customer read request
 *
 * This class represents an Twinfield customer read request.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CustomerReadRequest extends ReadRequest {
	/**
	 * Specifcy which article code to read.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * Constructs and initialize an Twinfield article read request.
	 *
	 * @param string $office  Specify from wich office to read.
	 * @param string $code    Specifcy which article code to read.
	 */
	public function __construct( $office, $code ) {
		parent::__construct( 'dimensions', $office );

		$this->code = $code;
	}

	/**
	 * Get the article read request code.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}
}
