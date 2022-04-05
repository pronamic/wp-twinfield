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
use Pronamic\WordPress\Twinfield\Dimensions\DimensionType;

/**
 * Contact
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield/Contacts
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Contact extends Dimension {
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
	 * Constructs and initialize a contact.
	 *
	 * @param DimensionType $type Dimension type.
	 * @param string        $code Dimension code.
	 */
	public function __construct( $type, $code ) {
		parent::__construct( $type, $code );

		$this->addresses = [];
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
