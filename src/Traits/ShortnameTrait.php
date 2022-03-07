<?php

namespace Pronamic\WordPress\Twinfield\Traits;

trait ShortnameTrait {
	/**
	 * Shortname.
	 *
	 * @var string|null
	 */
	private $shortname;

	/**
	 * Get shortname.
	 *
	 * @return string|null
	 */
	public function get_shortname() {
		return $this->shortname;
	}

	/**
	 * Set shortname.
	 *
	 * @param string|null $shortname The shortname.
	 */
	public function set_shortname( $shortname ) {
		$this->shortname = $shortname;
	}
}
