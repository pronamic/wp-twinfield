<?php
/**
 * Finder data
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Finder;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Finder data
 *
 * The FinderData class is a container for the search results.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class FinderData implements IteratorAggregate, JsonSerializable {
	/**
	 * The total number of search results.
	 *
	 * @var int
	 */
	private $total_rows;

	/**
	 * The column names.
	 *
	 * @var array
	 */
	private $columns;

	/**
	 * The requested data items.
	 *
	 * @var array
	 */
	private $items;

	/**
	 * Construct finder data.
	 * 
	 * @param int   $total_rows Total rows.
	 * @param array $columns    Columns.
	 * @param array $items      Items.
	 */
	public function __construct( $total_rows, $columns, $items ) {
		$this->total_rows = $total_rows;
		$this->columns    = $columns;
		$this->items      = $items;
	}

	/**
	 * Get the total number of search results.
	 *
	 * @return int
	 */
	public function get_total_rows() {
		return $this->total_rows;
	}

	/**
	 * Get the column names.
	 *
	 * @return array
	 */
	public function get_columns() {
		return $this->columns;
	}

	/**
	 * Get the requested data items.
	 *
	 * @return array
	 */
	public function get_items() {
		return $this->items;
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
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'total_rows' => $this->total_rows,
			'columns'    => $this->columns,
			'items'      => $this->items,
		];
	}

	/**
	 * From Twinfield object.
	 * 
	 * @param object $object Object.
	 * @return self
	 */
	public static function from_twinfield_object( $object ) {
		$data = ObjectAccess::from_object( $object );

		$columns = $data->get_object( 'Columns' )->get_array( 'string' );

		$items = \array_map(
			function( $object ) {
				return ObjectAccess::from_object( $object )->get_array( 'string' );
			},
			$data->get_object( 'Items' )->get_array( 'ArrayOfString' )
		);

		return new self(
			$data->get_property( 'TotalRows' ),
			$columns,
			$items
		);
	}
}
