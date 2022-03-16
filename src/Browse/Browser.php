<?php
/**
 * Browser
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Browse
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use Pronamic\WordPress\Twinfield\XMLProcessor;

/**
 * Browser
 *
 * This class utilizes Twinfield browse features.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Browser {
	/**
	 * Constructs and initializes a browser object.
	 *
	 * @param XMLProcessor $xml_processor Twinfield XML processor object.
	 */
	public function __construct( XMLProcessor $xml_processor ) {
		$this->xml_processor = $xml_processor;
	}

	/**
	 * Get browse read request by the specified request.
	 *
	 * @param BrowseReadRequest $request The browse read request.
	 * @return BrowseDefinition
	 */
	public function get_browse_definition( BrowseReadRequest $request ) {
		$response = $this->xml_processor->process_xml_string( $request->to_xml() );

		$string = $response->get_result();

		$xml = simplexml_load_string( $string );

		$browse_definition = new BrowseDefinition( $xml );

		return $browse_definition;
	}

	/**
	 * Get XML by the specified browse definition.
	 *
	 * @param BrowseDefinition $browse_definition The browse definition.
	 * @return string
	 */
	public function get_xml_string( BrowseDefinition $browse_definition ) {
		$string = $browse_definition->get_xml_columns()->asXML();

		$response = $this->xml_processor->process_xml_string( $string );

		return $response;
	}

	/**
	 * Get columns by the specified columns.
	 *
	 * @param BrowseDefinition $browse_definition The browse definition.
	 * @return \SimpleXMLElement
	 */
	public function get_data( BrowseDefinition $browse_definition ) {
		$string = $this->get_xml_string( $browse_definition );

		$xml = simplexml_load_string( $string );

		$data = new BrowseData( $xml );

		return $data;
	}
}
