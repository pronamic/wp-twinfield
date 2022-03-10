<?php
/**
 * Transaction
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use JsonSerializable;

/**
 * Transaction
 *
 * This class represents a Twinfield transaction.
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
class Transaction implements JsonSerializable {
	/**
	 * Websevice origin.
	 *
	 * @var string|null
	 */
	private $webservice_origin;

	/**
	 * Browse definition.
	 *
	 * @var BrowseDefinition|null
	 */
	private $browse_definition;

	/**
	 * Location.
	 *
	 * Indicate the destiny of the purchase transaction:
	 * `temporary` = purchase transaction is saved as `provisional`
	 * `final` = purchase transaction is saved as `final`
	 *
	 * @var string|null
	 */
	private $location;

	/**
	 * Header.
	 *
	 * @var TransactionHeader
	 */
	private $header;

	/**
	 * Lines.
	 *
	 * @var array
	 */
	private $lines;

	/**
	 * Constructs and initialize a Twinfield transaction.
	 */
	public function __construct() {
		$this->header = new TransactionHeader();
		$this->lines  = [];
	}

	public function get_transaction_type() {
		return $this->transaction_type;
	}

	public function get_number() {
		return $this->header->get_number();
	}

	/**
	 * Get webservice origin.
	 *
	 * @return string|null
	 */
	public function get_webservice_origin() {
		return $this->webservice_origin;
	}

	/**
	 * Set webservice origin.
	 *
	 * @param string|null $webservice_origin Webservice origin.
	 */
	public function set_webservice_origin( $webservice_origin ) {
		$this->webservice_origin = $webservice_origin;
	}

	/**
	 * Get browse definition.
	 *
	 * @return BrowseDefinition|null
	 */
	public function get_browse_definition() {
		return $this->browse_definition;
	}

	/**
	 * Set browse definition.
	 *
	 * @param BrowseDefinition|null $browse_definition Browse definition.
	 */
	public function set_browse_definition( $browse_definition ) {
		$this->browse_definition = $browse_definition;
	}

	/**
	 * Get location.
	 *
	 * @return string|null
	 */
	public function get_location() {
		return $this->location;
	}

	/**
	 * Set location.
	 *
	 * @param string|null $location Location.
	 */
	public function set_location( $location ) {
		$this->location = $location;
	}

	/**
	 * Get transaction header.
	 *
	 * @return TransactionHeader
	 */
	public function get_header() {
		return $this->header;
	}

	/**
	 * Get the transaction lines.
	 *
	 * @return array
	 */
	public function get_lines() {
		return $this->lines;
	}

	/**
	 * Add the specified line to this transaction.
	 *
	 * @param TransactionLine $line The transaction line to add.
	 */
	public function add_line( TransactionLine $line ) {
		$this->lines[] = $line;
	}

	/**
	 * Create a new transaction line.
	 *
	 * @return TransactionLine
	 */
	public function new_line() {
		$line = new TransactionLine( $this );

		$this->add_line( $line );

		return $line;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		$lines = array_map(
			function( $line ) {
				return [
					'id'   => $line->get_id(),
					'type' => $line->get_type(),
					'base_value'  => $line->get_base_value(),
					'open_base_value'   => $line->get_open_base_value(),
				];
			},
			$this->lines
		);

		return [
			'header' => [
				'office' => $this->get_transaction_type()->get_office()->get_code(),
				'code'   => $this->get_transaction_type()->get_code(),
				'number' => $this->get_header()->get_number(),
			],
			'lines'  => $lines,
		];
	}

	public function to_dom_document() {
		$document = new DOMDocument();

		$document->preserveWhiteSpace = false;
		$document->formatOutput       = true;

		$e_transaction = $document->appendChild( $document->createElement( 'transaction' ) );

		$e_header = $e_transaction->appendChild( $document->createElement( 'header' ) );

		$e_header->appendChild( $document->createElement( 'office', $this->transaction_type->get_office()->get_code() ) );
		$e_header->appendChild( $document->createElement( 'code', $this->transaction_type->get_code() ) );

		if ( null !== $this->currency ) {
			$e_header->appendChild( $document->createElement( 'currency', $this->currency->get_code() ) );
		}

		if ( null !== $this->date ) {
			$e_header->appendChild( $document->createElement( 'date', $this->date->format( 'Ymd' ) ) );
		}

		$e_lines = $e_transaction->appendChild( $document->createElement( 'lines' ) );

		foreach ( $this->lines as $line ) {
			$e_line = $e_lines->appendChild( $document->createElement( 'line' ) );

			$type = $line->get_type();

			if ( null !== $type ) {
				$e_line->setAttribute( 'type', $type );
			}

			$id = $line->get_id();

			if ( null !== $id ) {
				$e_line->setAttribute( 'id', $id );
			}

			$dimensions = [
				'dim1' => $line->get_dimension_1(),
				'dim2' => $line->get_dimension_2(),
				'dim3' => $line->get_dimension_3(),
			];

			foreach ( $dimensions as $name => $dimension ) {
				if ( null !== $dimension ) {
					$e_line->appendChild( $document->createElement( $name, $dimension->get_code() ) );
				}
			}

			$debit_credit = $line->get_debit_credit();

			if ( null !== $debit_credit ) {
				$e_line->appendChild( $document->createElement( 'debitcredit', $debit_credit ) );
			}

			$value = $line->get_value();

			if ( null !== $value ) {
				$e_line->appendChild( $document->createElement( 'value', $value ) );
			}

			$invoice_number = $line->get_invoice_number();

			if ( null !== $invoice_number ) {
				$e_line->appendChild( $document->createElement( 'invoicenumber', $invoice_number ) );
			}

			$description = $line->get_description();

			if ( null !== $description ) {
				$e_line->appendChild( $document->createElement( 'description', $description ) );
			}
		}

		return $document;
	}

	public function to_xml() {
		$document = $this->to_dom_document();

		return $document->saveXML();
	}
}
