<?php
/**
 * Dimension service
 *
 * @package Pronamic/WordPress/Twinfield/Dimensions
 */

namespace Pronamic\WordPress\Twinfield\Dimensions;

use Pronamic\WordPress\Twinfield\XMLProcessor;
use Pronamic\WordPress\Twinfield\Browse\Browser;
use Pronamic\WordPress\Twinfield\Browse\BrowseReadRequest;
use Pronamic\WordPress\Twinfield\XML\Transactions\BrowseTransactionsUnserializer;

/**
 * Dimension service class
 */
class DimensionService {
	/**
	 * The XML processor wich is used to connect with Twinfield.
	 *
	 * @var XMLProcessor
	 */
	private $xml_processor;

	/**
	 * Construct dimension service.
	 *
	 * @param XMLProcessor $xml_processor The XML processor to use within this sales invoice service object.
	 */
	public function __construct( XMLProcessor $xml_processor ) {
		$this->xml_processor = $xml_processor;
	}

	/**
	 * Get transaction.
	 *
	 * @param string $office The office to get the transaction from.
	 * @param string $code   The code of the transaction to retrieve.
	 * @param string $number The number of the transaction to retrieve.
	 * @return DimensionReadResponse
	 */
	public function get_dimension( $office_code, $dimension_type_code, $dimension_code ) {
		$dimension_read_request = new DimensionReadRequest( $office_code, $dimension_type_code, $dimension_code );

		$xml = $dimension_read_request->to_xml();

		$response = $this->xml_processor->process_xml_string( $xml );

		$organisation = $this->xml_processor->client->organisation;

		return new DimensionReadResponse( $response, $organisation );
	}
}
