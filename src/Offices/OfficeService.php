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
	 * @return OfficesXmlReader
	 */
	public function get_offices() {
		$xml_processor = $this->client->get_xml_processor();

		$offices_list_request = new OfficesListRequest();

		$xml = $xml_processor->process_xml_string( $offices_list_request->to_xml() );

		$offices_list_response = new OfficesListResponse( $this->client->get_organisation(), $xml );

		return $offices_list_response->to_offices();
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

		$response = $xml_processor->process_xml_string( $office_read_request->to_xml() );

		return Office::from_xml( $response, $office );
	}
}
