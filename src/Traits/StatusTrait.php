<?php
/**
 * Status trait
 *
 * @since 1.0.0
 * @package Pronamic/WordPress/Twinfield/Traits
 */

namespace Pronamic\WordPress\Twinfield\Traits;

trait StatusTrait {
	/**
	 * Status.
	 *
	 * @var string|null
	 */
	private $status;

	/**
	 * Get status.
	 *
	 * @return string|null
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Set status.
	 *
	 * @param string|null $status The status.
	 */
	public function set_status( $status ) {
		$this->status = $status;
	}
}
