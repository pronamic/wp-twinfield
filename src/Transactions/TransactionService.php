<?php
/**
 * Transaction Service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use Pronamic\WordPress\Twinfield\ProcessXmlString;
use Pronamic\WordPress\Twinfield\XMLProcessor;
use Pronamic\WordPress\Twinfield\Browse\Browser;
use Pronamic\WordPress\Twinfield\Browse\BrowseReadRequest;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Relations\Relation;
use Pronamic\WordPress\Twinfield\XML\Transactions\TransactionReadRequestSerializer;
use Pronamic\WordPress\Twinfield\XML\Transactions\TransactionUnserializer;
use Pronamic\WordPress\Twinfield\XML\Transactions\BrowseTransactionsUnserializer;

/**
 * Transaction Service
 *
 * This class represents an Twinfield transactions service.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class TransactionService {
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
		$this->browser       = new Browser( $xml_processor );
	}

	/**
	 * Get browse definiation.
	 *
	 * @param string $office_code The office code.
	 * @param string $browse_code The browse code.
	 * @return BrowseDefinition
	 */
	public function get_browse_definition( $office_code, $browse_code ) {
		$browse_read_request = new BrowseReadRequest( $office_code, $browse_code );

		$browse_definition = $this->browser->get_browse_definition( $browse_read_request );

		return $browse_definition;
	}

	/**
	 * Get transactions.
	 *
	 * @param BrowseDefinition $browse_definition The browse definition.
	 * @return array
	 */
	public function get_transactions( $browse_definition ) {
		$string = $this->browser->get_xml_string( $browse_definition );

		$xml = simplexml_load_string( $string );

		$unserializer = new BrowseTransactionsUnserializer( $browse_definition );

		$lines = $unserializer->unserialize( $xml );

		return $unserializer->get_transactions();
	}

	/**
	 * Get lines.
	 *
	 * @param BrowseDefinition $browse_definition The browse definition.
	 * @return array
	 */
	public function get_transaction_lines( $browse_definition ) {
		$lines = [];

		$string = $this->browser->get_xml_string( $browse_definition );

		$xml = simplexml_load_string( $string );

		$unserializer = new BrowseTransactionsUnserializer();

		$lines = $unserializer->unserialize( $xml );

		return $lines;
	}

	/**
	 * Get the specified transaction.
	 *
	 * @param string $office The office to get the transaction from.
	 * @param string $code   The code of the transaction to retrieve.
	 * @param string $number The number of the transaction to retrieve.
	 * @return Transaction
	 */
	public function get_transaction( $office, $code, $number ) {
		$result = null;

		$request = new TransactionReadRequestSerializer( new TransactionReadRequest( $office, $code, $number ) );

		$response = $this->xml_processor->process_xml_string( new ProcessXmlString( $request ) );

		$xml = simplexml_load_string( $response );

		if ( false !== $xml ) {
			$unserializer = new TransactionUnserializer();

			$result = $unserializer->unserialize( $xml );
		}

		return $result;
	}
}
