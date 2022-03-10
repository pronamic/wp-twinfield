<?php
/**
 * Transaction Type
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use Pronamic\WordPress\Twinfield\CodeName;
use Pronamic\WordPress\Twinfield\Transactions\Transaction;

/**
 * Transaction Type
 *
 * This class represents a Twinfield transaction type
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class TransactionType extends CodeName {
	private $office;

	private $transactions;

	public function __construct( $office, $code ) {
		parent::__construct( $code );

		$this->office       = $office;
		$this->transactions = [];
	}

	public function get_office() {
		return $this->office;
	}

	public function new_transaction( $number = null ) {
		$transaction = new Transaction();

		$transaction->transaction_type = $this;
		$transaction->get_header()->set_number( $number );

		$this->transactions[] = $transaction;

		return $transaction;
	}
}
