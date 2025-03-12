<?php
/**
 * Budget code
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Budget;

use JsonSerializable;
use Stringable;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Budget class
 */
class BudgetCode implements JsonSerializable, Stringable {
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
	 * Name.
	 * 
	 * @var string|null
	 */
	public ?string $name = null;

	/**
	 * Construct budget code.
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
			'office'    => $this->office,
			'code'      => $this->code,
			'name'      => $this->name,
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
