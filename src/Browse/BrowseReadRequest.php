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
	 * Specifcy which article code to read.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * Constructs and initialize a Twinfield browse read request.
	 *
	 * @param string $office  Specify from wich office to read.
	 * @param string $code    Specifcy which browse code to read.
	 */
	public function __construct( $office, $code ) {
		parent::__construct( 'browse', $office );

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

	public function to_xml() {
		$serializer = new \Pronamic\WordPress\Twinfield\XML\Browse\BrowseReadRequestSerializer( $this );

		return (string) $serializer;
	}
}
