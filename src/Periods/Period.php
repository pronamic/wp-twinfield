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
use DateTimeZone;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Period
 *
 * @since   1.0.0
 * @package Pronamic/WP/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class Period implements JsonSerializable {
	/**
	 * Year.
	 * 
	 * @var int
	 */
	public $year;

	/**
	 * Number.
	 * 
	 * @var int
	 */
	public $number;

	/**
	 * Name.
	 * 
	 * @var string|null
	 */
	public ?string $name;

	/**
	 * Is open.
	 * 
	 * @var bool|null
	 */
	public ?bool $is_open;

	/**
	 * Name.
	 * 
	 * @var string|null
	 */
	public ?DateTimeInterface $end_date;

	/**
	 * Construct period.
	 * 
	 * @param int                    $year     Year.
	 * @param int                    $number   Number.
	 * @param string|null            $name     Name.
	 * @param bool|null              $is_open  Is open.
	 * @param DateTimeInterface|null $end_date End date.
	 */
	public function __construct( $year, $number, $name = null, $is_open = null, DateTimeInterface $end_date = null ) {
		$this->year     = $year;
		$this->number   = $number;
		$this->name     = $name;
		$this->is_open  = $is_open;
		$this->end_date = $end_date;
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
	 * Get number.
	 * 
	 * @return int
	 */
	public function get_number() {
		return $this->number;
	}

	/**
	 * Get name.
	 * 
	 * @return string|null
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Is open.
	 * 
	 * @return bool|null
	 */
	public function is_open() {
		return $this->is_open;
	}

	/**
	 * Get end date.
	 * 
	 * @return DateTimeInterface|null
	 */
	public function get_end_date() {
		return $this->end_date;
	}

	/**
	 * From Twinfield object.
	 * 
	 * @param int    $year  Year.
	 * @param object $value Object.
	 */
	public static function from_twinfield_object( $year, $value ) {
		$data = ObjectAccess::from_object( $value );

		$period = new self(
			$year,
			$data->get_property( 'Number' ),
			$data->get_property( 'Name' ),
			$data->get_property( 'Open' )
		);

		$end_date_string = $data->get_property( 'EndDate' );

		if ( null !== $end_date_string ) {
			$result = DateTimeImmutable::createFromFormat( 'Y-m-d\TH:i:s', $end_date_string, new DateTimeZone( 'UTC' ) );

			if ( false === $result ) {
				throw new \Exception( 'Unknown period end date format: ' . $end_date_string );
			}

			$period->end_date = $result;
		}

		return $period;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'year'     => $this->year,
			'number'   => $this->number,
			'name'     => $this->name,
			'is_open'  => $this->is_open,
			'end_date' => null === $this->end_date ? null : $this->end_date->format( 'Y-m-d' ),
		];
	}
}
