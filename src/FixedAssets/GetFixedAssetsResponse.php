<?php
/**
 * Get fixed assets response
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Get fixed assets response class
 */
final class GetFixedAssetsResponse implements \IteratorAggregate, \Countable {
	/**
	 * Filtered total.
	 *
	 * @var int
	 */
	public readonly int $filtered_total;

	/**
	 * Total.
	 *
	 * @var int
	 */
	public readonly int $total;

	/**
	 * Items.
	 *
	 * @var FixedAsset[]
	 */
	public readonly array $items;

	/**
	 * Construct get fixed assets response.
	 *
	 * @param int            $filtered_total Filtered total.
	 * @param int            $total          Total.
	 * @param FixedAsset[]   $items          Items.
	 */
	private function __construct( int $filtered_total, int $total, array $items = [] ) {
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

	/**
	 * From JSON.
	 *
	 * @param string $value JSON.
	 * @return self
	 */
	public static function from_json( string $value ) {
		$data = \json_decode( $value );

		return self::from_object( $data );
	}

	/**
	 * Get iterator.
	 *
	 * @return \ArrayIterator<int, FixedAsset>
	 */
	public function getIterator(): \ArrayIterator {
		return new \ArrayIterator( $this->items );
	}

	/**
	 * Count items.
	 *
	 * @return int
	 */
	public function count(): int {
		return \count( $this->items );
	}
}
