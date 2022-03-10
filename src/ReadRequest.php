<?php
/**
 * Read request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Session
 *
 * This class represents an Twinfield session.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ReadRequest {
	/**
	 * Specify what type of data to read.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Specify from wich office to read.
	 *
	 * @var array<string, string>
	 */
	private $values;

	/**
	 * Constructs and initialize an Twinfield read request.
	 *
	 * @param array $values Values.
	 */
	public function __construct( $values = [] ) {
		$this->values = $values;
	}

	public function set( $name, $value ) {
		$this->values[ $name ] = $value;
	}

	public function to_dom_document() {
		$document = new \DOMDocument();

		// $document->preserveWhiteSpace = false;
		// $document->formatOutput       = true;

		$read_element = $document->appendChild( $document->createElement( 'read' ) );

		foreach ( $this->values as $name => $value ) {
			$read_element->appendChild( $document->createElement( $name, $value ) );
		}

		return $document;
	}

	public function to_xml() {
		$dom = $this->to_dom_document();

		return $dom->saveXML( $dom->documentElement );
	}

	public function __toString() {
		return $this->to_xml();
	}
}
