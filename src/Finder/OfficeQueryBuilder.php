<?php
/**
 * Office Query Builder
 *
 * @package Pronamic/WordPress/Twinfield/Finder
 */

namespace Pronamic\WordPress\Twinfield\Finder;

use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Office Query Builder
 *
 * Fluent interface for querying Twinfield offices.
 *
 * @package Pronamic/WordPress/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class OfficeQueryBuilder extends FinderQueryBuilder {
	/**
	 * Constructor.
	 *
	 * @param Finder $finder The finder instance.
	 */
	public function __construct( Finder $finder ) {
		parent::__construct( $finder, 'OFF' );
	}

	/**
	 * Include the office ID in the results.
	 *
	 * @return static
	 */
	public function includeId() {
		$this->options['includeid'] = '1';

		return $this;
	}

	/**
	 * Execute the query and get Office objects.
	 *
	 * @return Office[]
	 */
	public function getOffices(): array {
		$items = $this->items();
var_dump( $items );exit;
		$offices = [];

		foreach ( $items as $item ) {
			$offices[] = new Office( $item[0], $item[1] ?? null );
		}

		return $offices;
	}
}
