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
	public function __construct( $year, $period ) {
		$this->year   = $year;
		$this->period = $period;
	}

	public function get_year() {
		return $this->year;
	}

	public function get_period() {
		return $this->period;
	}

	public static function from_twinfield_object( $object ) {
		return new self( $object->Year, $object->Period );
	}
}
