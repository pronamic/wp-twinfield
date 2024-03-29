<?php
/**
 * Bank statement line
 *
 * @package Pronamic\WordPress\Twinfield
 */

namespace Pronamic\WordPress\Twinfield\BankStatements;

use DateTimeInterface;
use DateTimeImmutable;
use DateTimeZone;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Bank statement line class
 */
class BankStatementLine implements JsonSerializable {
	/**
	 * Line identifier.
	 * 
	 * @var int
	 */
	private $id;

	/**
	 * Basic bank account number (BBAN) of the contraparty.
	 * 
	 * @var string
	 */
	private $contra_account_number;

	/**
	 * International bank account number (IBAN) of the contraparty.
	 * 
	 * @var string
	 */
	private $contra_iban;

	/**
	 * Bank account name of the contraparty.
	 * 
	 * @var string
	 */
	private $contra_account_name;

	/**
	 * Transaction payment reference.
	 * 
	 * @var string
	 */
	private $payment_reference;

	/**
	 * Transaction amount.
	 * 
	 * @var string
	 */
	private $amount;

	/**
	 * Transaction amount in the base currency.
	 * 
	 * @var string
	 */
	private $base_amount;

	/**
	 * Transaction description.
	 * 
	 * @var string
	 */
	private $description;

	/**
	 * Transaction type identification code.
	 * 
	 * @var string
	 */
	private $transaction_type_id;

	/**
	 * Transaction reference of the bank.
	 * 
	 * @var string
	 */
	private $reference;

	/**
	 * Unique identification assigned by the initiating party.
	 * 
	 * @var string
	 */
	private $end_to_end_id;

	/**
	 * Return reason code for returned or rejected transaction.
	 * 
	 * @var string
	 */
	private $return_reason;

	/**
	 * Construct bank statement line.
	 *
	 * @param int    $id                    Line identifier.
	 * @param string $contra_account_number Basic bank account number (BBAN) of the contraparty.
	 * @param string $contra_iban           International bank account number (IBAN) of the contraparty.
	 * @param string $contra_account_name   Bank account name of the contraparty.
	 * @param string $payment_reference     Transaction payment reference.
	 * @param string $amount                Transaction amount.
	 * @param string $base_amount           Transaction amount in the base currency.
	 * @param string $description           Transaction description.
	 * @param string $transaction_type_id   Transaction type identification code.
	 * @param string $reference             Transaction reference of the bank.
	 * @param string $end_to_end_id         Unique identification assigned by the initiating party.
	 * @param string $return_reason         Return reason code for returned or rejected transaction.
	 */
	public function __construct( $id, $contra_account_number, $contra_iban, $contra_account_name, $payment_reference, $amount, $base_amount, $description, $transaction_type_id, $reference, $end_to_end_id, $return_reason ) {
		$this->id                    = $id;
		$this->contra_account_number = $contra_account_number;
		$this->contra_iban           = $contra_iban;
		$this->contra_account_name   = $contra_account_name;
		$this->payment_reference     = $payment_reference;
		$this->amount                = $amount;
		$this->base_amount           = $base_amount;
		$this->description           = $description;
		$this->transaction_type_id   = $transaction_type_id;
		$this->reference             = $reference;
		$this->end_to_end_id         = $end_to_end_id;
		$this->return_reason         = $return_reason;
	}

	/**
	 * Get line ID.
	 * 
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * From Twinfield object.
	 * 
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$bank_statement_line = new self(
			$data->get_property( 'LineId' ),
			$data->get_property( 'ContraAccountNumber' ),
			$data->get_property( 'ContraIban' ),
			$data->get_property( 'ContraAccountName' ),
			$data->get_property( 'PaymentReference' ),
			$data->get_property( 'Amount' ),
			$data->get_property( 'BaseAmount' ),
			$data->get_property( 'Description' ),
			$data->get_property( 'TransactionTypeId' ),
			$data->get_property( 'Reference' ),
			$data->get_property( 'EndToEndId' ),
			$data->get_property( 'ReturnReason' ),
		);

		return $bank_statement_line;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return (object) [
			'id'                    => $this->id,
			'contra_account_number' => $this->contra_account_number,
			'contra_iban'           => $this->contra_iban,
			'contra_account_name'   => $this->contra_account_name,
			'payment_reference'     => $this->payment_reference,
			'amount'                => $this->amount,
			'base_amount'           => $this->amount,
			'description'           => $this->description,
			'transaction_type_id'   => $this->transaction_type_id,
			'reference'             => $this->reference,
			'end_to_end_id'         => $this->end_to_end_id,
			'return_reason'         => $this->return_reason,
		];
	}
}
