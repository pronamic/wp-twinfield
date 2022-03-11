<?php
/**
 * Transaction Response
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use DOMDocument;
use DOMXPath;
use Pronamic\WordPress\Twinfield\XML\Transactions\TransactionUnserializer;

/**
 * Transaction Response
 *
 * This class represents a Twinfield transaction response.
 *
 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Transactions/BankTransactions
 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Transactions/CashTransactions
 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Transactions/JournalTransactions
 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/PurchaseTransactions
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class TransactionResponse {
	/**
	 * Transaction.
	 *
	 * @var Transaction
	 */
	private $transaction;

	/**
	 * Destiny.
	 *
	 * @var string
	 */
	private $destiny;

	/**
	 * Result.
	 *
	 * @var bool
	 */
	private $result;

	/**
	 * Errors.
	 *
	 * @var array
	 */
	private $errors;

	/**
	 * Construct transaction response.
	 *
	 * @param Transaction $transaction Transaction.
	 * @param string      $destiny     Destiny.
	 * @param array       $errors      Errors.
	 */
	public function __construct( Transaction $transaction, $destiny, $errors ) {
		$this->transaction = $transaction;
		$this->destiny     = $destiny;
		$this->errors      = $errors;
	}

	/**
	 * Is successfully?
	 *
	 * @return bool
	 */
	public function is_successfully() {
		return 0 === \count( $this->errors );
	}

	/**
	 * Get errors.
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Create from XML.
	 *
	 * @param string $xml XML.
	 * @return TransactionResponse
	 * @throws \Exception Throws exception when reading from XML fails.
	 */
	public static function from_xml( $xml ) {
		$document = new DOMDocument();

		$result = $document->loadXML( $xml );

		if ( false === $result ) {
			throw new \Exception( 'Could not load XML.' );
		}

		$transaction_unserializer = new TransactionUnserializer();

		$simplexml = \simplexml_load_string( $xml );

		$transaction = $transaction_unserializer->unserialize( $simplexml );

		$destiny = $document->documentElement->getAttribute( 'destiny' );

		$result = ( '1' === $document->documentElement->getAttribute( 'result' ) );

		$xpath = new DOMXPath( $document );

		/**
		 * Problem tags.
		 *
		 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Types/XmlWebServices#Parsing-results
		 */
		$problem_tags = $xpath->query( '//*[@result="0"]' );

		$errors = [];

		foreach ( $problem_tags as $problem_tag ) {
			$errors[] = (object) [
				'message' => $problem_tag->getAttribute( 'msg' ),
				'type'    => $problem_tag->getAttribute( 'msgtype' ),
				'element' => $problem_tag,
			];
		}

		$transaction_response = new self( $transaction, $destiny, $errors );

		return $transaction_response;
	}
}
