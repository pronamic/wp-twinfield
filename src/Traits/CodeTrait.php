<?php

namespace Pronamic\WordPress\Twinfield\Traits;

trait CodeTrait {
	/**
	 * Code.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * Get code.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Set code.
	 *
	 * @param string $code The code.
	 */
	public function set_code( $code ) {
		$this->code = $code;
	}
}
