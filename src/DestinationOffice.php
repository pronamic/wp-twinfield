<?php
/**
 * Destination office
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Destination office
 *
 * This class represents a Twinfield destination office.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DestinationOffice extends CodeName {
	/**
	 * Dimension 1.
	 *
	 * @var CodeName|null
	 */
	private $dimension_1;

	/**
	 * Get dimension 1.
	 *
	 * @return CodeName|null
	 */
	public function get_dimension_1() {
		return $this->dimension_1;
	}

	/**
	 * Set dimension 1.
	 *
	 * @param CodeName|null $dimension_1 Dimension 1.
	 */
	public function set_dimension_1( $dimension_1 ) {
		$this->dimension_1 = $dimension_1;
	}
}
