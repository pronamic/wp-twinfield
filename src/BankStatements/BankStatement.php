<?php
/**
 * Bank statement
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
 * Bank statement class
 */
class BankStatement implements JsonSerializable {
	/**
	 * Bank code.
	 * 
	 * @var string
	 */
	private $code;

	/**
	 * Statement number.
	 * 
	 * @var int
	 */
	private $number;

	/**
	 * Sub identifier in case the same statement number is imported twice.
	 * 
	 * @var int
	 */
	private $sub_id;

	/**
	 * Basic bank account number (BBAN).
	 * 
	 * @var string
	 */
	private $account_number;

	/**
	 * International bank account number (IBAN).
	 * 
	 * @var string
	 */
	private $iban;

	/**
	 * Statement date.
	 * 
	 * @var DateTimeInterface
	 */
	private $statement_date;

	/**
	 * Currency of the amounts. For instance "EUR".
	 * 
	 * @var string
	 */
	private $currency;

	/**
	 * Opening balance amount.
	 * 
	 * @var float
	 */
	private $opening_balance;

	/**
	 * Closing balance amount.
	 * 
	 * @var float
	 */
	private $closing_balance;

	/**
	 * Statement lines.
	 *
	 * @var BankStatementLine[]
	 */
	private $lines;

	/**
	 * Transaction number in case the statement is posted, else value will be null.
	 * 
	 * @var float|null
	 */
	private $transaction_number;

	/**
	 * Construct bank statement.
	 * 
	 * @param string            $code           Code.
	 * @param int               $number         Number.
	 * @param int               $sub_id         Sub ID.
	 * @param string            $account_number Account number.
	 * @param string            $iban           IBAN.
	 * @param DateTimeInterface $statement_date Statement date.
	 * @param string            $currency       Currency.
	 */
	public function __construct( $code, $number, $sub_id, $account_number, $iban, DateTimeInterface $statement_date, $currency, $opening_balance, $closing_balance ) {
		$this->code            = $code;
		$this->number          = $number;
		$this->sub_id          = $sub_id;
		$this->account_number  = $account_number;
		$this->iban            = $iban;
		$this->statement_date  = $statement_date;
		$this->currency        = $currency;
		$this->opening_balance = $opening_balance;
		$this->closing_balance = $closing_balance;

		$this->lines = [];
	}

	/**
	 * From Twinfield object.
	 * 
	 * @param object $object Object.
	 */
	public static function from_twinfield_object( $object ) {
		$data = ObjectAccess::from_object( $object );

		$bank_statement = new self(
			$data->get_property( 'Code' ),
			$data->get_property( 'Number' ),
			$data->get_property( 'SubId' ),
			$data->get_property( 'AccountNumber' ),
			$data->get_property( 'Iban' ),
			new DateTimeImmutable( $data->get_property( 'StatementDate' ), new DateTimeZone( 'UTC' ) ),
			$data->get_property( 'Currency' ),
			$data->get_property( 'OpeningBalance' ),
			$data->get_property( 'ClosingBalance' ),
		);

		$bank_statement->lines = \array_map(
			function( $object ) {
				return BankStatementLine::from_twinfield_object( $object );
			},
			$data->get_object( 'Lines' )->get_array( 'BankStatementLine' )
		);

		$bank_statement->transaction_number = $data->get_property( 'TransactionNumber' );

		return $bank_statement;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'code'               => $this->code,
			'number'             => $this->number,
			'sub_id'             => $this->sub_id,
			'account_number'     => $this->account_number,
			'iban'               => $this->iban,
			'statement_date'     => $this->statement_date->format( \DATE_ATOM ),
			'currency'           => $this->currency,
			'opening_balance'    => $this->opening_balance,
			'closing_balance'    => $this->closing_balance,
			'lines'              => $this->lines,
			'transaction_number' => $this->transaction_number,
		];
	}
}