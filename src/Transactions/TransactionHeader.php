<?php
/**
 * Transaction Header
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use DateTimeInterface;
use Pronamic\WordPress\Twinfield\Currency;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Relations\Relation;
use Pronamic\WordPress\Twinfield\Users\User;

/**
 * Transaction Header
 *
 * This class represents a Twinfield transaction header.
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
class TransactionHeader {
	/**
	 * Office.
	 *
	 * @var string
	 */
	private $office;

	/**
	 * Code.
	 *
	 * @var TransactionTypeCode
	 */
	private $code;

	/**
	 * Statement number.
	 *
	 * Bank transactions - Number of the bank statement. Don't confuse this number with the `transaction number`.
	 * Cash transactions - Cash transactions number. Don't confuse this number with the `transaction number`.
	 *
	 * @var int|null
	 */
	private $statement_number;

	/**
	 * Start value.
	 *
	 * Opening balance. If not provided, the opening balance is set to zero.
	 *
	 * @var string|null
	 */
	private $start_value;

	/**
	 * Close value.
	 *
	 * Closing balance. If not provided, the closing balance is set to zero.
	 *
	 * @var string|null
	 */
	private $close_value;

	/**
	 * User.
	 *
	 * @var User|null
	 */
	private $user;

	/**
	 * Relation.
	 * 
	 * @var Relation|null
	 */
	public $relation;

	/**
	 * Status.
	 * 
	 * @var string|null
	 */
	public $status;

	/**
	 * Due date.
	 * 
	 * @var DateTimeInterface|null
	 */
	public $due_date;

	/**
	 * Constructs and initializes a transaction header.
	 */
	public function __construct() {
	}

	/**
	 * Get office.
	 *
	 * @return string
	 */
	public function get_office() {
		return $this->office;
	}

	/**
	 * Set office.
	 *
	 * @param string $office The office.
	 */
	public function set_office( $office ) {
		$this->office = $office;
	}

	/**
	 * Get code.
	 *
	 * @return TransactionTypeCode
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Set code.
	 *
	 * @param TransactionTypeCode $code The code.
	 */
	public function set_code( $code ) {
		$this->code = $code;
	}

	/**
	 * Get number.
	 *
	 * @return string
	 */
	public function get_number() {
		return $this->number;
	}

	/**
	 * Set number.
	 *
	 * @param string $number The number.
	 */
	public function set_number( $number ) {
		$this->number = $number;
	}

	/**
	 * Get currency.
	 *
	 * @return Currency
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Set currency.
	 *
	 * @param Currency $currency The currency.
	 */
	public function set_currency( Currency $currency = null ) {
		$this->currency = $currency;
	}

	/**
	 * Get regime.
	 *
	 * @return string|null
	 */
	public function get_regime() {
		return $this->regime;
	}

	/**
	 * Set regime.
	 *
	 * @param string $regime The regime.
	 */
	public function set_regime( $regime ) {
		$this->regime = $regime;
	}

	/**
	 * Get date.
	 *
	 * @return \DateTimeInterface
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * Set date.
	 *
	 * @param \DateTimeInterface $date The date.
	 */
	public function set_date( \DateTimeInterface $date = null ) {
		$this->date = $date;
	}

	/**
	 * Get statement number.
	 *
	 * @return int|null
	 */
	public function get_statement_number() {
		return $this->statement_number;
	}

	/**
	 * Set statement number.
	 *
	 * @param int|null $statement_number Statement number.
	 */
	public function set_statement_number( $statement_number ) {
		$this->statement_number = $statement_number;
	}

	/**
	 * Get start value.
	 *
	 * @return string|null
	 */
	public function get_start_value() {
		return $this->start_value;
	}

	/**
	 * Set start value.
	 *
	 * @param string|null $value Value.
	 */
	public function set_start_value( $value ) {
		$this->start_value = $value;
	}

	/**
	 * Get close value.
	 *
	 * @return string|null
	 */
	public function get_close_value() {
		return $this->close_value;
	}

	/**
	 * Set close value.
	 *
	 * @param string|null $value Value.
	 */
	public function set_close_value( $value ) {
		$this->close_value = $value;
	}

	/**
	 * Get the user.
	 *
	 * @return User|null
	 */
	public function get_user() {
		return $this->user;
	}

	/**
	 * Set the user.
	 *
	 * @param User|null $user User.
	 */
	public function set_user( $user ) {
		$this->user = $user;
	}

	/**
	 * Get the input date of this transaction line.
	 *
	 * @return \DateTimeInterface
	 */
	public function get_input_date() {
		return $this->input_date;
	}

	/**
	 * Set the input date of this transaction line.
	 *
	 * @param \DateTimeInterface $date The input date.
	 */
	public function set_input_date( \DateTimeInterface $date ) {
		$this->input_date = $date;
	}

	/**
	 * Get the due date of this transaction header.
	 *
	 * @return \DateTimeInterface
	 */
	public function get_due_date() {
		return $this->due_date;
	}

	/**
	 * Set the due date of this transaction header.
	 *
	 * @param \DateTimeInterface $date The input date.
	 */
	public function set_due_date( \DateTimeInterface $date ) {
		$this->due_date = $date;
	}

	/**
	 * Get the modification date of this transaction header.
	 *
	 * @return \DateTimeInterface
	 */
	public function get_modification_date() {
		return $this->modification_date;
	}

	/**
	 * Set the modification date of this transaction header.
	 *
	 * @param \DateTimeInterface $date The modification date.
	 */
	public function set_modification_date( \DateTimeInterface $date ) {
		$this->modification_date = $date;
	}

	/**
	 * Get relation.
	 *
	 * @return Relation
	 */
	public function get_relation() {
		return $this->relation;
	}

	/**
	 * Set relation.
	 *
	 * @param Relation $relation Relation.
	 */
	public function set_relation( Relation $relation = null ) {
		$this->relation = $relation;
	}

	/**
	 * Get the status of this transaction line.
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Set the status of this transaction line.
	 *
	 * @param string $status The status.
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}

	/**
	 * Get username.
	 *
	 * @return string
	 */
	public function get_username() {
		return $this->username;
	}

	/**
	 * Set year.
	 *
	 * @param string $username Username.
	 */
	public function set_username( $username ) {
		$this->username = $username;
	}

	/**
	 * Get year.
	 *
	 * @return int
	 */
	public function get_year() {
		return $this->year;
	}

	/**
	 * Set year.
	 *
	 * @param int $year The year.
	 */
	public function set_year( $year ) {
		$this->year = $year;
	}

	/**
	 * Get period.
	 *
	 * @return int
	 */
	public function get_period() {
		return $this->period;
	}

	/**
	 * Set period.
	 *
	 * @param int $period The period.
	 */
	public function set_period( $period ) {
		$this->period = $period;
	}

	/**
	 * Get origin.
	 *
	 * @return string|null
	 */
	public function get_origin() {
		return $this->origin;
	}

	/**
	 * Set origin.
	 *
	 * @param string|null $origin Origin.
	 */
	public function set_origin( $origin ) {
		$this->origin = $origin;
	}

	/**
	 * Get origin reference.
	 *
	 * @return string|null
	 */
	public function get_origin_reference() {
		return $this->origin_reference;
	}

	/**
	 * Set origin reference.
	 *
	 * @param string|null $origin_reference Origin reference.
	 */
	public function set_origin_reference( $origin_reference ) {
		$this->origin_reference = $origin_reference;
	}

	/**
	 * Get the invoice number of this transaction header.
	 *
	 * @return string
	 */
	public function get_invoice_number() {
		return $this->invoice_number;
	}

	/**
	 * Set the invoice number of this transaction header.
	 *
	 * @param string $invoice_number The invoice number.
	 */
	public function set_invoice_number( $invoice_number ) {
		$this->invoice_number = $invoice_number;
	}

	/**
	 * Get free text 1.
	 *
	 * @return string
	 */
	public function get_free_text_1() {
		return $this->free_text_1;
	}

	/**
	 * Set free text 1.
	 *
	 * @param string $text Text.
	 */
	public function set_free_text_1( $text ) {
		$this->free_text_1 = $text;
	}

	/**
	 * Get free text 2.
	 *
	 * @return string
	 */
	public function get_free_text_2() {
		return $this->free_text_2;
	}

	/**
	 * Set free text 2.
	 *
	 * @param string $text Text.
	 */
	public function set_free_text_2( $text ) {
		$this->free_text_2 = $text;
	}

	/**
	 * Get free text 3.
	 *
	 * @return string
	 */
	public function get_free_text_3() {
		return $this->free_text_3;
	}

	/**
	 * Set free text 2.
	 *
	 * @param string $text Text.
	 */
	public function set_free_text_3( $text ) {
		$this->free_text_3 = $text;
	}
}
