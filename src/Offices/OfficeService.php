<?php
/**
 * Office Service
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
 * Office Service
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class OfficeService {
	/**
	 * Client.
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * Construct office service.
	 *
	 * @param Client $client Client.
	 */
	public function __construct( Client $client ) {
		$this->client = $client;
	}

	/**
	 * Get offices.
	 *
	 * @return array
	 */
	public function get_offices() {
		$result = null;

		$request = '<list><type>offices</type></list>';

		$document = new \DOMDocument();

		$list_element = $document->createElement( 'list' );

		$document->appendChild( $list_element );

		$type_element = $document->createElement( 'type' );
		$type_element->appendChild( new \DOMText( 'offices' ) );

		$list_element->appendChild( $type_element );

		$xml_string = $document->saveXML();

		$xml_processor = $this->client->get_xml_processor();

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $xml_string ) );

		$xml = simplexml_load_string( $response );

		if ( false !== $xml ) {
			$result = [];

			foreach ( $xml->office as $element ) {
				$office = $this->client->organisation->new_office( Security::filter( $element ) );

				$office->set_name( Security::filter( $element['name'] ) );
				$office->set_shortname( Security::filter( $element['shortname'] ) );

				$result[] = $office;
			}
		}

		return $result;
	}

	/**
	 * Get office.
	 *
	 * @param Office $office Office.
	 * @return Office
	 */
	public function get_office( $office ) {
		$office_read_request = new OfficeReadRequest( $office->get_code() );

		$xml_processor = $this->client->get_xml_processor();

		$xml_processor->set_office( $office );

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $office_read_request->to_xml() ) );

		return Office::from_xml( \strval( $response ), $office );
	}
}
