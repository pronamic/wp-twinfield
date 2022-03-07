<?php

namespace Pronamic\WordPress\Twinfield\Traits;

trait CreatedTrait {
	/**
	 * Created.
	 *
	 * @var null|\DateTimeImmutable
	 */
	private $created_at;

	/**
	 * Get created at datetime object.
	 *
	 * @return null|\DateTimeImmutable
	 */
	public function get_created_at() {
		return $this->created_at;
	}

	/**
	 * Set created at datetime object.
	 *
	 * @param null|\DateTimeImmutable 
	 */
	public function set_created_at( \DateTimeImmutable $created_at = null ) {
		$this->created_at = $created_at;
	}
}
