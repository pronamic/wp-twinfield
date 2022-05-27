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
	 * ne identifier.
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
	 * Construct bank statement line.
	 * 
	 * @param int $id Line identifier.
	 */
	public function __construct( $id, $contra_account_number, $contra_iban, $contra_account_name, $payment_reference, $amount, $base_amount, $description ) {
		$this->id                    = $id;
		$this->contra_account_number = $contra_account_number;
		$this->contra_iban           = $contra_iban;
		$this->contra_account_name   = $contra_account_name;
		$this->payment_reference     = $payment_reference;
		$this->amount                = $amount;
		$this->base_amount           = $base_amount;
		$this->description           = $description;
	}

	/**
	 * From Twinfield object.
	 * 
	 * @param object $object Object.
	 */
	public static function from_twinfield_object( $object ) {
		$data = ObjectAccess::from_object( $object );

		$bank_statement_line = new self(
			$data->get_property( 'LineId' ),
			$data->get_property( 'ContraAccountNumber' ),
			$data->get_property( 'ContraIban' ),
			$data->get_property( 'ContraAccountName' ),
			$data->get_property( 'PaymentReference' ),
			$data->get_property( 'Amount' ),
			$data->get_property( 'BaseAmount' ),
			$data->get_property( 'Description' ),
		);

		return $bank_statement_line;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'id'                    => $this->id,
			'contra_account_number' => $this->contra_account_number,
			'contra_iban'           => $this->contra_iban,
			'contra_account_name'   => $this->contra_account_name,
			'payment_reference'     => $this->payment_reference,
			'amount'                => $this->amount,
			'base_amount'           => $this->amount,
			'description'           => $this->description,
		];
	}
}
