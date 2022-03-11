<?php
/**
 * Finder data
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Finder;

use IteratorAggregate;
use JsonSerializable;

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
	 * @var ArrayOfString
	 */
	private $columns;

	/**
	 * The requested data items.
	 *
	 * @var ArrayOfArrayOfString
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
	 * @return ArrayOfString
	 */
	public function get_columns() {
		return $this->columns;
	}

	/**
	 * Get the requested data items.
	 *
	 * @return ArrayOfArrayOfString
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Get iterator.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return $this->items->getIterator();
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
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		return new self( $object->TotalRows, $object->Columns, $object->Items );
	}
}
