<?php
/**
 * Customer financials
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Customers;

use InvalidArgumentException;
use Pronamic\WordPress\Twinfield\EmailList;

/**
 * Customer financials
 *
 * This class represents Twinfield customer financials.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CustomerFinancials {
	/**
	 * The number of due days.
	 *
	 * @var int
	 */
	private $due_days;

	/**
	 * Determines if the sales invoices will be sent electronically to the customer.
	 *
	 * @var boolean
	 */
	private $ebilling;

	/**
	 * The mail address the electronic sales invoice is sent to.
	 *
	 * @var EmailList
	 */
	private $ebillmail;

	/**
	 * Get the number of due days.
	 *
	 * @return int
	 */
	public function get_due_days() {
		return $this->due_days;
	}

	/**
	 * Set the number of due days.
	 *
	 * @param int $due_days The due days.
	 */
	public function set_due_days( $due_days ) {
		$this->due_days = $due_days;
	}

	/**
	 * Get ebilling flag.
	 *
	 * @return boolean
	 */
	public function get_ebilling() {
		return $this->ebilling;
	}

	/**
	 * Set ebilling flag.
	 *
	 * @param boolean $ebilling The ebilling flag.
	 */
	public function set_ebilling( $ebilling ) {
		$this->ebilling = $ebilling;
	}

	/**
	 * Get ebillmail.
	 *
	 * @return EmailList
	 */
	public function get_ebillmail() {
		return $this->ebillmail;
	}

	/**
	 * Set ebillmail.
	 *
	 * @param EmailList|string $value The ebill mail.
	 * @throws InvalidArgumentException If the provided argument is invalid.
	 */
	public function set_ebillmail( $value ) {
		if ( is_string( $value ) ) {
			$value = new EmailList( $value );
		}

		if ( ! $value instanceof EmailList ) {
			throw new InvalidArgumentException();
		}

		$this->ebillmail = $value;
	}
}
