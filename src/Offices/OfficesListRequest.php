<?php
/**
 * Offices List Request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\ProcessXmlString;
use Pronamic\WordPress\Twinfield\XMLProcessor;
use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Offices List Request
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class OfficesListRequest {
	public function to_dom_document() {
		$document = new \DOMDocument();

		// $document->preserveWhiteSpace = false;
		// $document->formatOutput       = true;

		$element = $document->appendChild( $document->createElement( 'list' ) );

		$element->appendChild( $document->createElement( 'type', 'offices' ) );

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
