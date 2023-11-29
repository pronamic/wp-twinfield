<?php
/**
 * Document Time Frame
 *
 * @since   1.0.0
 *
 * @package Pronamic/WP/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Declarations;

/**
 * Document Time Frame
 *
 * @since   1.0.0
 * @package Pronamic/WP/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class DocumentTimeFrame {
	/**
	 * Construct document time frame.
	 * 
	 * @param int $year   Year.
	 * @param int $period Period.
	 */
	public function __construct( $year, $period ) {
		$this->year   = $year;
		$this->period = $period;
	}

	/**
	 * Get year.
	 * 
	 * @return int
	 */
	public function get_year() {
		return $this->year;
	}

	/**
	 * Get period.
	 * 
	 * @return int
	 */
	public function get_period() {
		return $this->period;
	}

	/**
	 * Create document time frame from Twinfield object.
	 * 
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Twinfield vaiable name.
		return new self( $value->Year, $value->Period );
	}
}
