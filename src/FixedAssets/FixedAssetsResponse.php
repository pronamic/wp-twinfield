<?php
/**
 * Fixed assets response
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Fixed assets response class
 */
final class FixedAssetsResponse {
	/**
	 * Filtered total.
	 *
	 * @var int
	 */
	public int $filtered_total;

	/**
	 * Total.
	 *
	 * @var int
	 */
	public int $total;

	/**
	 * Items.
	 *
	 * @var FixedAsset[]
	 */
	public array $items;

	/**
	 * Construct fixed assets response.
	 *
	 * @param int            $filtered_total Filtered total.
	 * @param int            $total          Total.
	 * @param FixedAsset[]   $items          Items.
	 */
	public function __construct( int $filtered_total, int $total, array $items ) {
		$this->filtered_total = $filtered_total;
		$this->total          = $total;
		$this->items          = $items;
	}

	/**
	 * From object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$items = [];

		$items_data = $data->get_optional( 'items' );

		if ( null !== $items_data && \is_array( $items_data ) ) {
			foreach ( $items_data as $item_data ) {
				$items[] = FixedAsset::from_object( $item_data );
			}
		}

		return new self(
			$data->get_property( 'filteredTotal' ),
			$data->get_property( 'total' ),
			$items
		);
	}
}
