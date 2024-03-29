<?php
/**
 * Customer finder result
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Customers;

use Pronamic\WordPress\Twinfield\Contacts\Contact;

/**
 * Customer finder result
 *
 * This class represents an Twinfield customer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CustomerFinderResult {
	/**
	 * Code.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * Name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Bank account number.
	 *
	 * @var string
	 */
	private $bank_account_number;

	/**
	 * Constructs and initializes an customer find result.
	 */
	public function __construct() {
	}

	/**
	 * Get code.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Set code.
	 *
	 * @param string $code The code.
	 */
	public function set_code( $code ) {
		$this->code = $code;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set name.
	 *
	 * @param string $name The name.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * Get bank account number.
	 *
	 * @return string
	 */
	public function get_bank_account_number() {
		return $this->bank_account_number;
	}

	/**
	 * Set bank account number.
	 *
	 * @param string $number The bank account number.
	 */
	public function set_bank_account_number( $number ) {
		$this->bank_account_number = $number;
	}
}
