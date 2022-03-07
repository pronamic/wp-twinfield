<?php

namespace Pronamic\WordPress\Twinfield\Traits;

trait NameTrait {
	/**
	 * Name.
	 *
	 * @var string|null
	 */
	private $name;

	/**
	 * Get name.
	 *
	 * @return string|null
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set name.
	 *
	 * @param string|null $name The name.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}
}
