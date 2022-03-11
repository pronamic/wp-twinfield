<?php
/**
 * Sales Invoice Service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\SalesInvoices;

use Pronamic\WordPress\Twinfield\ProcessXmlString;
use Pronamic\WordPress\Twinfield\XMLProcessor;
use Pronamic\WordPress\Twinfield\XML\SalesInvoices\SalesInvoiceSerializer;
use Pronamic\WordPress\Twinfield\XML\SalesInvoices\SalesInvoiceUnserializer;

/**
 * Sales Invoice Service
 *
 * This class represents an Twinfield sales invoice service.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SalesInvoiceService {
	/**
	 * The XML processor wich is used to connect with Twinfield.
	 *
	 * @var XMLProcessor
	 */
	private $xml_processor;

	/**
	 * Constructs and initializes an sales invoice service.
	 *
	 * @param XMLProcessor $xml_processor The XML processor to use within this sales invoice service object.
	 */
	public function __construct( XMLProcessor $xml_processor ) {
		$this->xml_processor = $xml_processor;
	}

	/**
	 * Get the specified sales invoice.
	 *
	 * @param string $office The office to get the sales invoice from.
	 * @param string $code   The code of the sales invoice to retrieve.
	 * @param string $number The number of the salies invoice to retrieve.
	 * @return SalesInvoiceResponse
	 */
	public function get_sales_invoice( $office, $code, $number ) {
		$result = null;

		$request = new SalesInvoiceReadRequest( $office, $code, $number );

		$response = $this->xml_processor->process_xml_string( new ProcessXmlString( $request->to_xml() ) );

		$xml = simplexml_load_string( $response );

		if ( false !== $xml ) {
			$unserializer = new SalesInvoiceUnserializer();

			$result = $unserializer->unserialize( $xml );
		}

		return $result;
	}

	/**
	 * Insert the specifiekd sales invoice.
	 *
	 * @param SalesInvoice $sales_invoice The sales invoice to insert.
	 * @return SalesInvoiceResponse
	 */
	public function insert_sales_invoice( SalesInvoice $sales_invoice ) {
		$result = null;

		$xml = new SalesInvoiceSerializer( $sales_invoice );

		$response = $this->xml_processor->process_xml_string( new ProcessXmlString( $xml ) );

		$xml = simplexml_load_string( $response );

		if ( false !== $xml ) {
			$unserializer = new SalesInvoiceUnserializer();

			$result = $unserializer->unserialize( $xml );
		}

		return $result;
	}

	/**
	 * Update the specified sales invoice.
	 *
	 * This function is based on the WordPress `wp_update_post` function.
	 * https://github.com/WordPress/WordPress/blob/4.3/wp-includes/post.php#L3607-L3665
	 *
	 * @param SalesInvoice $sales_invoice The sales invoice to update.
	 * @return SalesInvoiceResponse
	 */
	public function update_sales_invoice( SalesInvoice $sales_invoice ) {
		$header = $sales_invoice->get_header();

		// @see https://github.com/WordPress/WordPress/blob/4.3/wp-includes/post.php#L3627-L3628
		$response = $this->get_sales_invoice( $header->get_office(), $header->get_code(), $heaer->get_number() );

		if ( 0 === $response->get_result() ) {
			return false;
		}
	}
}
