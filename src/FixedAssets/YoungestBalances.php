<?php
/**
 * Youngest balances
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Youngest balances class
 */
final class YoungestBalances {
	/**
	 * Net book value.
	 *
	 * @var float|null
	 */
	public ?float $net_book_value;

	/**
	 * Purchase value.
	 *
	 * @var float|null
	 */
	public ?float $purchase_value;

	/**
	 * Construct youngest balances.
	 *
	 * @param float|null $net_book_value Net book value.
	 * @param float|null $purchase_value Purchase value.
	 */
	public function __construct( ?float $net_book_value = null, ?float $purchase_value = null ) {
		$this->net_book_value = $net_book_value;
		$this->purchase_value = $purchase_value;
	}

	/**
	 * From object.
	 *
	 * @param object|null $value Object.
	 * @return self|null
	 */
	public static function from_object( $value ) {
		if ( null === $value ) {
			return null;
		}

		$data = ObjectAccess::from_object( $value );

		return new self(
			$data->get_optional( 'netBookValue' ),
			$data->get_optional( 'purchaseValue' )
		);
	}
}
