<?php
/**
 * Period
 *
 * @since   1.0.0
 *
 * @package Pronamic/WP/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Periods;

use DateTimeInterface;
use DateTimeImmutable;

/**
 * Period
 *
 * @since   1.0.0
 * @package Pronamic/WP/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class Period {
	public function __construct( $year, $number, $name, $is_open, DateTimeInterface $end_date = null ) {
		$this->year     = $year;
		$this->number   = $number;
		$this->name     = $name;
		$this->is_open  = $is_open;
		$this->end_date = $end_date;
	}

	public function get_year() {
		return $this->year;
	}

	public function get_number() {
		return $this->number;
	}

	public function get_name() {
		return $this->name;
	}

	public function is_open() {
		return $this->is_open;
	}

	public function get_end_date() {
		return $this->end_date;
	}

	public static function from_twinfield_object( $year, $object ) {
		return new self(
			$year,
			$object->Number,
			$object->Name,
			$object->Open,
			null === $object->EndDate ? null : new DateTimeImmutable( $object->EndDate )
		);
	}
}
