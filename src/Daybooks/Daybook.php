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
	 * Name.
	 * 
	 * @var string|null
	 */
	public ?string $name = null;

	/**
	 * Shortname.
	 * 
	 * @var string|null
	 */
	public ?string $shortname = null;

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
	 * Get code.
	 * 
	 * @deprecated
	 * @return string
	 */
	public function get_code(): string {
		return $this->code;
	}

	/**
	 * Get name.
	 * 
	 * @deprecated
	 * @return string|null
	 */
	public function get_name(): ?string {
		return $this->name;
	}

	/**
	 * Get shortname.
	 * 
	 * @deprecated
	 * @return string|null
	 */
	public function get_shortname(): ?string {
		return $this->shortname;
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
			'shortname' => $this->shortname,
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
