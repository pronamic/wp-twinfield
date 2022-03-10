<?php
/**
 * Browse read request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use Pronamic\WordPress\Twinfield\ReadRequest;

/**
 * Browse read request
 *
 * This class represents a Twinfield browse read request.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class BrowseReadRequest extends ReadRequest {
	/**
	 * Constructs and initialize a Twinfield browse read request.
	 *
	 * @param string $office  Specify from wich office to read.
	 * @param string $code    Specifcy which browse code to read.
	 */
	public function __construct( $office, $code ) {
		parent::__construct(
			[
				'type'   => 'browse',
				'office' => $office,
				'code'   => $code,
			]
		);
	}
}
