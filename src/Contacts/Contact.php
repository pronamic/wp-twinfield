<?php
/**
 * Contact
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Contacts
 */

namespace Pronamic\WordPress\Twinfield\Contacts;

use Pronamic\WordPress\Twinfield\Address;
use Pronamic\WordPress\Twinfield\Dimensions\Dimension;

/**
 * Contact
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield/Contacts
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Contact extends Dimension {
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
	 * Name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Website.
	 *
	 * @var string
	 */
	private $website;

	/**
	 * Addresses
	 *
	 * @var array
	 */
	private $addresses;

	/**
	 * Constructs and initialize an contact.
	 */
	public function __construct( $type, $code ) {
		parent::__construct( $type, $code );

		$this->addresses = array();
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
	 * Create and add a new address.
	 *
	 * @return Address
	 */
	public function new_address() {
		$address = new Address();

		/*
		 * Twinfield requires one default address:
		 * "There has to be one default address."
		 */
		if ( empty( $this->addresses ) ) {
			$address->set_default( true );
		}

		$this->addresses[] = $address;

		return $address;
	}

	/**
	 * Get the addresses of this contact.
	 *
	 * @return array An array with addresses.
	 */
	public function get_addresses() {
		return $this->addresses;
	}

	/**
	 * Get address by number.
	 *
	 * @param int $number Adress number.
	 * @return Address|null
	 */
	public function get_address_by_number( $number ) {
		foreach ( $this->addresses as $address ) {
			if ( $address->get_id() === $number ) {
				return $address;
			}
		}

		return null;
	}
}
