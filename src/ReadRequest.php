<?php
/**
 * Read request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use DOMDocument;

/**
 * Session
 *
 * This class represents an Twinfield session.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ReadRequest implements \Stringable {
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

	/**
	 * Convert to DOMDocument.
	 * 
	 * @return DOMDocument
	 */
	public function to_dom_document() {
		$document = new DOMDocument();

		$read_element = $document->appendChild( $document->createElement( 'read' ) );

		foreach ( $this->values as $name => $value ) {
			$read_element->appendChild( $document->createElement( $name, $value ) );
		}

		return $document;
	}

	/**
	 * Convert to XML string.
	 * 
	 * @return string
	 */
	public function to_xml() {
		$dom = $this->to_dom_document();

		return $dom->saveXML( $dom->documentElement );
	}

	/**
	 * To string.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		return $this->to_xml();
	}
}
