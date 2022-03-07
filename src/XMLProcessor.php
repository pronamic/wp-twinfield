<?php
/**
 * XML Processor
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;

/**
 * XML Processor
 *
 * This class connects to the Twinfield XML processor Webservices to process XML messages.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class XMLProcessor extends AbstractService {
	/**
	 * The Twinfield process XML WSDL URL.
	 *
	 * @var string
	 */
	public const WSDL_FILE = '/webservices/processxml.asmx?wsdl';

	/**
	 * Constructs and initializes an XML processor object.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		parent::__construct( self::WSDL_FILE, $client );
	}

	/**
	 * Send the specified XML string to Twinfield for processing.
	 *
	 * @param ProcessXmlString $xml The XML string to process by Twinfield.
	 * @return string
	 */
	public function process_xml_string( ProcessXmlString $xml ) {
		$soap_client = $this->get_soap_client();

		$response = $soap_client->ProcessXmlString( $xml );

		return $response;
	}
}
