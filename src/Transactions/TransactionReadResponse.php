<?php
/**
 * Transaction read response
 *
 * @package Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use DOMDocument;
use DOMXPath;
use Pronamic\WordPress\Twinfield\XML\ProblemTagException;

/**
 * Transaction read response class
 */
class TransactionReadResponse {
	/**
	 * XML.
	 * 
	 * @var string
	 */
	public $xml;

	/**
	 * Construct ttansaction read response.
	 *
	 * @param string $xml XML.
	 */
	public function __construct( string $xml ) {
		$this->xml = $xml;
	}

	/**
	 * Decode transaction read response.
	 * 
	 * @return Transaction
	 */
	public function transaction(): Transaction {
		$document = new DOMDocument();

		$result = $document->loadXML( $this->xml );

		if ( false === $result ) {
			throw new \Exception( 'Could not load XML.' );
		}

		$xpath = new DOMXPath( $document );

		/**
		 * Problem tags.
		 *
		 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Types/XmlWebServices#Parsing-results
		 */
		$problem_tags = $xpath->query( '//*[@result="0"]' );

		$exception = null;

		foreach ( $problem_tags as $problem_tag ) {
			$exception = new ProblemTagException( $problem_tag, 0, $exception );
		}

		if ( null !== $exception ) {
			throw $exception;
		}

		$transaction_unserializer = new TransactionUnserializer();

		$simplexml = \simplexml_load_string( $this->xml );

		$transaction = $transaction_unserializer->unserialize( $simplexml );

		return $transaction;
	}
}
