<?php
/**
 * Office Finder
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Offices
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use Pronamic\WordPress\Twinfield\Finder;
use Pronamic\WordPress\Twinfield\FinderTypes;
use Pronamic\WordPress\Twinfield\Search;
use Pronamic\WordPress\Twinfield\SearchFields;

/**
 * Office Finder
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class OfficeFinder {
	/**
	 * The finder wich is used to connect with Twinfield.
	 *
	 * @var Finder
	 */
	private $finder;

	/**
	 * Constructs and initializes an custom finder object.
	 *
	 * @param Finder $finder The finder.
	 */
	public function __construct( Finder $finder ) {
		$this->finder = $finder;
	}

	/**
	 * Find customers.
	 *
	 * @param string $pattern   The pattern.
	 * @param string $field     The field.
	 * @param int    $first_row The first row.
	 * @param int    $max_rows  The max rows.
	 */
	public function get_offices( $pattern, $field, $first_row, $max_rows ) {
		$offices = [];

		// Request.
		$search = new Search(
			FinderTypes::OFF,
			$pattern,
			$field,
			$first_row,
			$max_rows
		);

		$response = $this->finder->search( $search );

		// Parse.
		if ( $response ) {
			if ( $response->is_successful() ) {
				$data = $response->get_data();

				$items = $data->get_items();

				if ( ! is_null( $items ) ) {
					foreach ( $items as $item ) {
						$office = new Office( $item[0], $item[1] );

						$offices[] = $office;
					}
				}
			}
		}

		return $offices;
	}
}
