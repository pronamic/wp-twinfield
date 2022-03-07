<?php
/**
 * Dimension Read Request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Accounting;

use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\ProcessXmlString;
use Pronamic\WordPress\Twinfield\XMLProcessor;
use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Dimension Read Request
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DimensionReadRequest {
	/**
	 * Constructs and initializes an sales invoice service.
	 *
	 * @param XMLProcessor $xml_processor The XML processor.
	 */
	public function __construct( $office, $type, $code ) {
		$this->office = $office;
		$this->type   = $type;
		$this->code   = $code;
	}

	public function to_dom_document() {
		$document = new \DOMDocument();

		//$document->preserveWhiteSpace = false;
		//$document->formatOutput       = true;

		$read_element = $document->appendChild( $document->createElement( 'read' ) );

		$read_element->appendChild( $document->createElement( 'type', 'dimensions' ) );
		$read_element->appendChild( $document->createElement( 'office', $this->office ) );
		$read_element->appendChild( $document->createElement( 'dimtype', $this->type ) );
		$read_element->appendChild( $document->createElement( 'code', $this->code ) );

		return $document;
	}

	public function to_xml() {
		$dom =$this->to_dom_document();

		return $dom->saveXML( $dom->documentElement );
	}

	public function __toString() {
		return $this->to_xml();
	}
}
