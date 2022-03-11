<?php
/**
 * UID trait
 *
 * @since 1.0.0
 * @package Pronamic/WordPress/Twinfield/Traits
 */

namespace Pronamic\WordPress\Twinfield\Traits;

trait UniqueIdentificationTrait {
	/**
	 * Unique identification.
	 *
	 * @var string
	 */
	private $uid;

	/**
	 * Get unique identification.
	 *
	 * @return string
	 */
	public function get_uid() {
		return $this->uid;
	}

	/**
	 * Set unique identification.
	 *
	 * @param string $uid The unique identification.
	 */
	public function set_uid( $uid ) {
		$this->uid = $uid;
	}
}
