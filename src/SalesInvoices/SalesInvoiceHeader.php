<?php
/**
 * Sales Invoice Header
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\SalesInvoices;

use JsonSerializable;

/**
 * Sales Invoice Header
 *
 * This class represents an Twinfield sales invoice header.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SalesInvoiceHeader implements JsonSerializable {
	/**
	 * Office.
	 *
	 * @var string
	 */
	private $office;

	/**
	 * Type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Number
	 *
	 * @var string
	 */
	private $number;

	/**
	 * Date
	 *
	 * @var \DateTimeInterface
	 */
	private $date;

	/**
	 * Due date
	 *
	 * @var \DateTimeInterface
	 */
	private $due_date;

	/**
	 * Bank
	 *
	 * @var string
	 */
	private $bank;

	/**
	 * Customer
	 *
	 * @var string
	 */
	private $customer;

	/**
	 * Payment method.
	 *
	 * @var string
	 */
	private $payment_method;

	/**
	 * Header text.
	 *
	 * @var string
	 */
	private $header_text;

	/**
	 * Footer text.
	 *
	 * @var string
	 */
	private $footer_text;

	/**
	 * Invoice address number.
	 *
	 * @var int|null
	 */
	private $invoice_address_number;

	/**
	 * Deliver address number.
	 *
	 * @var int|null
	 */
	private $deliver_address_number;

	/**
	 * Constructs and initializes an sales invoice header.
	 */
	public function __construct() {
		$this->set_status( SalesInvoiceStatus::STATUS_CONCEPT );
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
	 * Set office
	 *
	 * @param string $office The office.
	 */
	public function set_office( $office ) {
		$this->office = $office;
	}

	/**
	 * Get type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set type
	 *
	 * @param string $type The type.
	 */
	public function set_type( $type ) {
		$this->type = $type;
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
	 * Set number
	 *
	 * @param string $number The number.
	 */
	public function set_number( $number ) {
		$this->number = $number;
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
	 * Get due date.
	 *
	 * @return \DateTimeInterface
	 */
	public function get_due_date() {
		return $this->due_date;
	}

	/**
	 * Set due date.
	 *
	 * @param \DateTimeInterface $due_date The due date.
	 */
	public function set_due_date( \DateTimeInterface $due_date = null ) {
		$this->due_date = $due_date;
	}

	/**
	 * Get bank.
	 *
	 * @return string
	 */
	public function get_bank() {
		return $this->bank;
	}

	/**
	 * Set bank.
	 *
	 * @param string $bank The bank.
	 */
	public function set_bank( $bank ) {
		$this->bank = $bank;
	}

	/**
	 * Get customer.
	 *
	 * @return string
	 */
	public function get_customer() {
		return $this->customer;
	}

	/**
	 * Set customer.
	 *
	 * @param string $customer The customer.
	 */
	public function set_customer( $customer ) {
		$this->customer = $customer;
	}

	/**
	 * Get status.
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Set status.
	 *
	 * @param string $status The status.
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}

	/**
	 * Get payment method.
	 *
	 * @return string
	 */
	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * Set payment method.
	 *
	 * @param string $payment_method The payment method.
	 */
	public function set_payment_method( $payment_method ) {
		$this->payment_method = $payment_method;
	}

	/**
	 * Get header text.
	 *
	 * @return string
	 */
	public function get_header_text() {
		return $this->header_text;
	}

	/**
	 * Set header text.
	 *
	 * @param string $text The header text.
	 */
	public function set_header_text( $text ) {
		$this->header_text = $text;
	}

	/**
	 * Get footer text.
	 *
	 * @return string
	 */
	public function get_footer_text() {
		return $this->footer_text;
	}

	/**
	 * Set footer text.
	 *
	 * @param string $text The footer text.
	 */
	public function set_footer_text( $text ) {
		$this->footer_text = $text;
	}

	/**
	 * Get invoice address number.
	 *
	 * @return int|null
	 */
	public function get_invoice_address_number() {
		return $this->invoice_address_number;
	}

	/**
	 * Set invoice address number.
	 *
	 * @param int|null $number Invoice address number.
	 */
	public function set_invoice_address_number( $number ) {
		$this->invoice_address_number = $number;
	}

	/**
	 * Get deliver address number.
	 *
	 * @return int|null
	 */
	public function get_deliver_address_number() {
		return $this->deliver_address_number;
	}

	/**
	 * Set footer text.
	 *
	 * @param int|null $number Deliver address number.
	 */
	public function set_deliver_address_number( $number ) {
		$this->deliver_address_number = $number;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return array(
			'office'                 => $this->office,
			'invoice_type'           => $this->type,
			'invoice_number'         => $this->number,
			'invoice_date'           => $this->date->format( \DATE_ATOM ),
			'due_date'               => $this->date->format( \DATE_ATOM ),
			'invoice_address_number' => $this->invoice_address_number,
			'deliver_address_number' => $this->deliver_address_number,
		);
	}
}
