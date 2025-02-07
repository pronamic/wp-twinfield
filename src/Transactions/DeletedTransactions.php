<?php
/**
 * Deleted transactions
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Deleted transactions
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DeletedTransactions implements Countable, IteratorAggregate, JsonSerializable {
	/**
	 * Items.
	 *
	 * @var DeletedTransaction[]
	 */
	private $items = [];

	/**
	 * Count.
	 * 
	 * @return int
	 */
	public function count(): int {
		return \count( $this->items );
	}

	/**
	 * Get iterator.
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator( $this->items );
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return DeletedTransaction[]
	 */
	public function jsonSerialize() {
		return $this->items;
	}

	/**
	 * Create office list from XML.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		$data = new ObjectAccess( $value );

		$deleted_transactions_data = new ObjectAccess( $data->get_property( 'DeletedTransactions' ) );

		$list = new self();

		if ( $deleted_transactions_data->has_property( 'DeletedTransaction' ) ) {
			$items = $deleted_transactions_data->get_array( 'DeletedTransaction' );

			foreach ( $items as $item ) {
				$list->items[] = DeletedTransaction::from_twinfield_object( $item );
			}
		}

		return $list;
	}
}
