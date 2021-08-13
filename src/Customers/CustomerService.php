<?php
/**
 * Customer Service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Customers;

use Pronamic\WordPress\Twinfield\ProcessXmlString;
use Pronamic\WordPress\Twinfield\XMLProcessor;

use Pronamic\WordPress\Twinfield\Browse\Browser;
use Pronamic\WordPress\Twinfield\Browse\BrowseCodes;
use Pronamic\WordPress\Twinfield\Browse\BrowseReadRequest;

use Pronamic\WordPress\Twinfield\XML\Customers\CustomerReadRequestSerializer;
use Pronamic\WordPress\Twinfield\XML\Customers\CustomerSerializer;
use Pronamic\WordPress\Twinfield\XML\Customers\CustomerUnserializer;

use Pronamic\WordPress\Twinfield\XML\Dimensions\DimensionUnserializer;

/**
 * Customer Service
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CustomerService {
	/**
	 * The XML processor wich is used to connect with Twinfield.
	 *
	 * @var XMLProcessor
	 */
	private $xml_processor;

	/**
	 * Constructs and initializes an sales invoice service.
	 *
	 * @param XMLProcessor $xml_processor The XML processor.
	 */
	public function __construct( XMLProcessor $xml_processor ) {
		$this->xml_processor = $xml_processor;
		$this->browser       = new Browser( $xml_processor );
	}

	/**
	 * Get all customers.
	 *
	 * @param string $office The office.
	 * @return array
	 */
	public function get_customers( $office ) {
		$customers = array();

		$xml = '<?xml version="1.0"?>
		<list>
			<office>' . $office . '</office>
			<type>dimensions</type>
			<dimtype>DEB</dimtype>
		</list>
		';

		$response = $this->xml_processor->process_xml_string( new ProcessXmlString( $xml ) );

		$xml = simplexml_load_string( $response );

		if ( false !== $xml ) {
			foreach ( $xml->dimension as $dimension ) {
				$customer = new \stdClass();

				$customer->code      = (string) $dimension;
				$customer->name      = (string) $dimension['name'];
				$customer->shortname = (string) $dimension['shortname'];

				$customers[] = $customer;
			}
		}

		return $customers;
	}

	/**
	 * Get the specified customer.
	 *
	 * @param string $office The office.
	 * @param string $code   The code.
	 * @return Customer
	 */
	public function get_customer( $office, $code ) {
		$result = null;

		$request = new CustomerReadRequestSerializer( new CustomerReadRequest( $office, $code ) );

		$response = $this->xml_processor->process_xml_string( new ProcessXmlString( $request ) );

		$xml = simplexml_load_string( $response );

		if ( false !== $xml ) {
			$unserializer = new CustomerUnserializer();

			$result = $unserializer->unserialize( $xml );
		}

		return $result;
	}

	/**
	 * Insert the specified customer.
	 *
	 * @param Customer $customer The customer.
	 * @return Customer
	 */
	public function insert_customer( Customer $customer ) {
		$xml = new CustomerSerializer( $customer );

		$response = $this->xml_processor->process_xml_string( new ProcessXmlString( $xml ) );

		$xml = simplexml_load_string( $response );

		if ( false !== $xml ) {
			$unserializer = new DimensionUnserializer();

			$result = $unserializer->unserialize( $xml );
		}

		return $result;
	}

	/**
	 * Update the specified customer.
	 *
	 * This function is based on the WordPress `wp_update_post` function.
	 * https://github.com/WordPress/WordPress/blob/4.3/wp-includes/post.php#L3607-L3665
	 *
	 * @param Customer $customer The customer to update.
	 * @return boolean
	 */
	public function update_customer( Customer $customer ) {
		// @see https://github.com/WordPress/WordPress/blob/4.3/wp-includes/post.php#L3627-L3628
		$original = $this->get_customer( $customer->get_office(), $customer->get_code() );

		if ( null === $original ) {
			return false;
		}
	}
}
