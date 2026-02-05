<?php
/**
 * Remote parameter name attribute
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

use Attribute;

/**
 * Remote parameter name attribute
 *
 * Used to map request properties to remote API parameter names.
 */
#[Attribute]
final class RemoteParameterName {
	/**
	 * Parameter name.
	 *
	 * @var string
	 */
	public string $name;

	/**
	 * Construct remote parameter name.
	 *
	 * @param string $name Parameter name.
	 */
	public function __construct( string $name ) {
		$this->name = $name;
	}
}
