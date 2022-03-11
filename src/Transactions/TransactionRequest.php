<?php
/**
 * Transaction Request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use DOMDocument;

/**
 * Transaction Request
 *
 * This class represents a Twinfield transaction request.
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
class TransactionRequest {
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
	 * Construct transaction request.
	 *
	 * @param Transaction $transaction Transaction.
	 * @param string      $destiny     Destiny.
	 */
	public function __construct( Transaction $transaction, $destiny ) {
		$this->transaction = $transaction;
		$this->destiny     = $destiny;
	}

	/**
	 * Create DOMDocument.
	 *
	 * @return DOMDocument
	 */
	public function to_dom_document() {
		$document = $this->transaction->to_dom_document();

		$document->documentElement->setAttribute( 'destiny', $this->destiny );

		return $document;
	}

	/**
	 * Create XML string.
	 *
	 * @return string
	 */
	public function to_xml() {
		$document = $this->to_dom_document();

		return $document->saveXML();
	}
}
