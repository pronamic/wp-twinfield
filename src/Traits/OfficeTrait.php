<?php
/**
 * Office trait
 *
 * @since 1.0.0
 * @package Pronamic/WordPress/Twinfield/Traits
 */

namespace Pronamic\WordPress\Twinfield\Traits;

use Pronamic\WordPress\Twinfield\Offices\Office;

trait OfficeTrait {
	/**
	 * Office.
	 *
	 * @var Office
	 */
	protected $office;

	/**
	 * Get office.
	 *
	 * @return Office
	 */
	public function get_office() {
		return $this->office;
	}

	/**
	 * Set office.
	 *
	 * @param Office $office Office.
	 */
	public function set_office( $office ) {
		$this->office = $office;
	}
}
