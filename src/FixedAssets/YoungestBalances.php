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
	 * @var string|null
	 */
	public readonly ?string $net_book_value;

	/**
	 * Purchase value.
	 *
	 * @var string|null
	 */
	public readonly ?string $purchase_value;

	/**
	 * Construct youngest balances.
	 *
	 * @param string|null $net_book_value Net book value.
	 * @param string|null $purchase_value Purchase value.
	 */
	private function __construct( ?string $net_book_value = null, ?string $purchase_value = null ) {
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

		$net_book_value = $data->get_optional( 'netBookValue' );
		$purchase_value = $data->get_optional( 'purchaseValue' );

		return new self(
			null !== $net_book_value ? (string) $net_book_value : null,
			null !== $purchase_value ? (string) $purchase_value : null
		);
	}
}
