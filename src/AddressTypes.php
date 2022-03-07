<?php
/**
 * Address types
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Address types
 *
 * This class contains constants for different Twinfield address types.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class AddressTypes {
	/**
	 * Adress type invoice.
	 *
	 * @var string
	 */
	public const INVOICE = 'invoice';

	/**
	 * Adress type postal.
	 *
	 * @var string
	 */
	public const POSTAL = 'postal';

	/**
	 * Adress type contact.
	 *
	 * @var string
	 */
	public const CONTACT = 'contact';
}
