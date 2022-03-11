<?php
/**
 * Touched trait
 *
 * @since 1.0.0
 * @package Pronamic/WordPress/Twinfield/Traits
 */

namespace Pronamic\WordPress\Twinfield\Traits;

trait TouchedTrait {
	/**
	 * Touched.
	 * 
	 * Count of the number of times the object is changed.
	 *
	 * @var int|null
	 */
	private $touched;

	/**
	 * Get touched.
	 *
	 * @return int|null
	 */
	public function get_touched() {
		return $this->touched;
	}

	/**
	 * Set touched.
	 *
	 * @param int|null $touched The number of touches.
	 */
	public function set_touched( $touched ) {
		$this->touched = $touched;
	}
}
