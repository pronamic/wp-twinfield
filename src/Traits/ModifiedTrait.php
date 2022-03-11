<?php
/**
 * Modified trait
 *
 * @since 1.0.0
 * @package Pronamic/WordPress/Twinfield/Traits
 */

namespace Pronamic\WordPress\Twinfield\Traits;

trait ModifiedTrait {
	/**
	 * Modified.
	 *
	 * @var null|\DateTimeImmutable
	 */
	private $modified_at;

	/**
	 * Get modified at datetime object.
	 *
	 * @return null|\DateTimeImmutable
	 */
	public function get_modified_at() {
		return $this->modified_at;
	}

	/**
	 * Set modified at datetime object.
	 *
	 * @param null|\DateTimeImmutable $modified_at Date/time.
	 */
	public function set_modified_at( \DateTimeImmutable $modified_at = null ) {
		$this->modified_at = $modified_at;
	}
}
