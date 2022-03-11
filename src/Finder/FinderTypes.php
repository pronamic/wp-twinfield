<?php
/**
 * Finder types
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Finder;

/**
 * Finder types
 *
 * This class contains constants for different Twinfield finder types.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class FinderTypes {
	/**
	 * Twinfield finder type constant for "Items".
	 * Return fields: code, name
	 *
	 * @var string
	 */
	public const ART = 'ART';

	/**
	 * Twinfield finder type constant for "Asset methods".
	 * Return fields: code, name
	 *
	 * @var string
	 */
	public const ASM = 'ASM';

	/**
	 * Twinfield finder type constant for "Budgets".
	 * Return fields: code, name
	 *
	 * @var string
	 */
	public const BDS = 'BDS';

	/**
	 * Twinfield finder type constant for "Dimensions".
	 * Return fields: code, name, bank account number, address fields
	 *
	 * @var string
	 */
	public const DIM = 'DIM';

	/**
	 * Twinfield finder type constant for "Offices".
	 * Return fields: code, name
	 *
	 * @var string
	 */
	public const OFF = 'OFF';
}
