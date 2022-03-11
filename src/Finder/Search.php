<?php
/**
 * Search
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Finder;

use Pronamic\WordPress\Twinfield\ArrayOfArrayOfString;

/**
 * Search
 *
 * This class represents an Twinfield search request.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Search {
	/**
	 * Finder type, see Finder type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The search pattern. May contain wildcards * and ?.
	 *
	 * @var string
	 */
	private $pattern;

	/**
	 * Fields to search through, see Search fields.
	 *
	 * @var int
	 */
	private $field;

	/**
	 * First row to return, usefull for paging.
	 *
	 * @var int
	 */
	private $first_row;

	/**
	 * Maximum number of rows to return, usefull for paging.
	 *
	 * @var int
	 */
	private $max_rows;

	/**
	 * Options.
	 *
	 * @var ArrayOfArrayOfString
	 */
	private $options;

	/**
	 * Constructs and initializes an Twinfield search object.
	 *
	 * @param string $type      Finder type, see Finder type.
	 * @param string $pattern   The search pattern. May contain wildcards * and ?.
	 * @param int    $field     Fields to search through, see Search fields.
	 * @param int    $first_row First row to return, usefull for paging.
	 * @param int    $max_rows  Maximum number of rows to return, usefull for paging.
	 * @param array  $options   The options.
	 */
	public function __construct( $type, $pattern, $field, $first_row, $max_rows, $options = [] ) {
		$this->type      = $type;
		$this->pattern   = $pattern;
		$this->field     = $field;
		$this->first_row = $first_row;
		$this->max_rows  = $max_rows;
		$this->options   = ArrayOfArrayOfString::parse_array( $options );
	}

	/**
	 * Convert to Twinfield object.
	 *
	 * @return array
	 */
	public function to_twinfield_object() {
		return [
			'type'     => $this->type,
			'pattern'  => $this->pattern,
			'field'    => $this->field,
			'firstRow' => $this->first_row,
			'maxRows'  => $this->max_rows,
			'options'  => $this->options,
		];
	}
}
