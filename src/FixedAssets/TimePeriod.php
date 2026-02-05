<?php
/**
 * Time period
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Time period class
 */
final class TimePeriod {
	/**
	 * Year.
	 *
	 * @var int|null
	 */
	public ?int $year;

	/**
	 * Period number.
	 *
	 * @var int|null
	 */
	public ?int $period_number;

	/**
	 * Construct time period.
	 *
	 * @param int|null $year          Year.
	 * @param int|null $period_number Period number.
	 */
	public function __construct( ?int $year = null, ?int $period_number = null ) {
		$this->year          = $year;
		$this->period_number = $period_number;
	}

	/**
	 * From object.
	 *
	 * @param object|null $value Object.
	 * @return self|null
	 */
	public static function from_object( $value ) {
		if ( null === $value ) {
			return null;
		}

		$data = ObjectAccess::from_object( $value );

		return new self(
			$data->get_optional( 'year' ),
			$data->get_optional( 'periodNumber' )
		);
	}
}
