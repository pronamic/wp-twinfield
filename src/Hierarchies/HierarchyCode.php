<?php
/**
 * Hierarchy code
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use JsonSerializable;
use Stringable;

/**
 * Hierarchy code class
 */
final class HierarchyCode implements JsonSerializable, Stringable {
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
	 * Construct hierarchy code.
	 * 
	 * @param string $code   Code.
	 */
	public function __construct( string $code ) {
		$this->code = $code;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'code' => $this->code,
			'name' => $this->name,
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
