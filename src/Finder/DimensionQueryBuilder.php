<?php
/**
 * Dimension Query Builder
 *
 * @package Pronamic/WordPress/Twinfield/Finder
 */

namespace Pronamic\WordPress\Twinfield\Finder;

use DateTimeInterface;

/**
 * Dimension Query Builder
 *
 * Fluent interface for querying Twinfield dimensions.
 *
 * @package Pronamic/WordPress/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class DimensionQueryBuilder extends FinderQueryBuilder {
	/**
	 * Constructor.
	 *
	 * @param Finder $finder The finder instance.
	 */
	public function __construct( Finder $finder ) {
		parent::__construct( $finder, FinderTypes::DIM );
	}

	/**
	 * Set the dimension type.
	 *
	 * @param string $dimension_type Dimension type (e.g., 'DEB', 'CRD', 'KPL', 'AST', 'PRJ').
	 * @return static
	 */
	public function type( string $dimension_type ) {
		$this->options['dimtype'] = $dimension_type;

		return $this;
	}

	/**
	 * Query for customers (debtors).
	 *
	 * @return static
	 */
	public function customers() {
		return $this->type( 'DEB' );
	}

	/**
	 * Query for suppliers (creditors).
	 *
	 * @return static
	 */
	public function suppliers() {
		return $this->type( 'CRD' );
	}

	/**
	 * Query for cost centers.
	 *
	 * @return static
	 */
	public function costCenters() {
		return $this->type( 'KPL' );
	}

	/**
	 * Query for fixed assets.
	 *
	 * @return static
	 */
	public function fixedAssets() {
		return $this->type( 'AST' );
	}

	/**
	 * Query for projects.
	 *
	 * @return static
	 */
	public function projects() {
		return $this->type( 'PRJ' );
	}

	/**
	 * Include hidden dimensions.
	 *
	 * @return static
	 */
	public function includeHidden() {
		$this->options['includehidden'] = '1';

		return $this;
	}

	/**
	 * Only return dimensions modified since a specific date.
	 *
	 * @param DateTimeInterface|string $date DateTime object or relative date string (e.g., '-1 year').
	 * @return static
	 */
	public function modifiedSince( $date ) {
		if ( is_string( $date ) ) {
			$date = new \DateTimeImmutable( $date, new \DateTimeZone( 'UTC' ) );
		}

		if ( $date instanceof DateTimeInterface ) {
			$this->options['modifiedsince'] = $date->format( 'YmdHis' );
		}

		return $this;
	}

	/**
	 * Filter by company.
	 *
	 * @param string $company Company code or wildcard.
	 * @return static
	 */
	public function company( string $company ) {
		$this->options['company'] = $company;

		return $this;
	}
}
