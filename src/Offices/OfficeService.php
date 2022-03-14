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
	 * @return OfficesList
	 */
	public function get_offices() {
		$xml_processor = $this->client->get_xml_processor();

		$offices_list_request = new OfficesListRequest();

		$offices_list_response = $xml_processor->process_xml_string( $offices_list_request->to_xml() );

		return OfficesList::from_xml( (string) $offices_list_response, $this->client->get_organisation() );
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
