<?php
/**
 * Customer
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Customers;

use Pronamic\WordPress\Twinfield\Contacts\Contact;

/**
 * Customer
 *
 * This class represents an Twinfield customer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Customer extends Contact {
	/**
	 * Financials.
	 *
	 * @var CustomerFinancials
	 */
	private $financials;

	/**
	 * Credit management.
	 *
	 * @var CustomerCreditManagement
	 */
	private $credit_management;

	/**
	 * Constructs and initializes an customer/
	 */
	public function __construct( $code ) {
		parent::__construct( 'DEB', $code );

		$this->financials        = new CustomerFinancials();
		$this->credit_management = new CustomerCreditManagement();
	}

	/**
	 * Get financials.
	 *
	 * @return CustomerFinancials
	 */
	public function get_financials() {
		return $this->financials;
	}

	/**
	 * Get credit management.
	 *
	 * @return CustomerCreditManagement
	 */
	public function get_credit_management() {
		return $this->credit_management;
	}
}
