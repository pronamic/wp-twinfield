<?php
/**
 * Finder data
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

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
class FinderData implements JsonSerializable {
	/**
	 * The total number of search results.
	 *
	 * @var int
	 */
	private $TotalRows; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * The column names.
	 *
	 * @var ArrayOfString
	 */
	private $Columns; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * The requested data items.
	 *
	 * @var ArrayOfArrayOfString
	 */
	private $Items; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * Get the total number of search results.
	 *
	 * @return int
	 */
	public function get_total_rows() {
		return $this->TotalRows; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.
	}

	/**
	 * Get the column names.
	 *
	 * @return ArrayOfString
	 */
	public function get_columns() {
		return $this->Columns; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.
	}

	/**
	 * Get the requested data items.
	 *
	 * @return ArrayOfArrayOfString
	 */
	public function get_items() {
		return $this->Items; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'total_rows' => $this->TotalRows,
			'columns'    => $this->Columns,
			'items'      => $this->Items,
		];
	}
}
