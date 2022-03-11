<?php
/**
 * Organisation trait
 *
 * @since 1.0.0
 * @package Pronamic/WordPress/Twinfield/Traits
 */

namespace Pronamic\WordPress\Twinfield\Traits;

use Pronamic\WordPress\Twinfield\Organisations\Organisation;

trait OrganisationTrait {
	/**
	 * Organisation.
	 *
	 * @var Organisation
	 */
	protected $organisation;

	/**
	 * Get organisation.
	 *
	 * @return Office
	 */
	public function get_organisation() {
		return $this->organisation;
	}

	/**
	 * Set organisation.
	 *
	 * @param Organisation $organisation Office.
	 */
	public function set_organisation( $organisation ) {
		$this->organisation = $organisation;
	}
}
