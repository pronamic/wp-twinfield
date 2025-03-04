<?php
/**
 * Daybook
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Daybooks;

use JsonSerializable;
use Stringable;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Daybook class
 * 
 * Also as `journal` or `transaction type`.
 * 
 * @link https://github.com/pronamic/wp-twinfield/issues/3
 */
class Daybook implements JsonSerializable, Stringable {
	/**
	 * Office.
	 * 
	 * @var Office
	 */
	public Office $office;

	/**
	 * Code.
	 * 
	 * @var string
	 */
	public string $code;

	/**
	 * Construct daybook.
	 * 
	 * @param Office $office Office.
	 * @param string $code   Code.
	 */
	public function __construct( Office $office, string $code ) {
		$this->office = $office;
		$this->code   = $code;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'office' => $this->office,
			'code'   => $this->code,
		];
	}

	/**
	 * To string.
	 * 
	 * @return string
	 */
	public function __toString(): string {
		return $this->code;
	}
}
