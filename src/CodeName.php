<?php
/**
 * Code name
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use JsonSerializable;
use Pronamic\WordPress\Twinfield\Traits\CodeTrait;
use Pronamic\WordPress\Twinfield\Traits\NameTrait;
use Pronamic\WordPress\Twinfield\Traits\ShortnameTrait;

/**
 * Code name
 *
 * This class represents a Twinfield code and name.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CodeName implements JsonSerializable {
	use CodeTrait;

	use NameTrait;

	use ShortnameTrait;

	/**
	 * Universally unique identifier.
	 *
	 * @var string|null
	 */
	private $uuid;

	/**
	 * Construct a code/name object.
	 *
	 * @param string      $code      Code.
	 * @param string|null $name      Name.
	 * @param string|null $shortname Shortname.
	 */
	public function __construct( $code, $name = null, $shortname = null ) {
		$this->set_code( $code );
		$this->set_name( $name );
		$this->set_shortname( $shortname );
	}

	/**
	 * Get UUID.
	 *
	 * @return string|null
	 */
	public function get_uuid() {
		return $this->uuid;
	}

	/**
	 * Set UUID.
	 *
	 * @param string|null $uuid The UUID.
	 */
	public function set_uuid( $uuid ) {
		$this->uuid = $uuid;
	}

	/**
	 * String.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->get_code();
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return (object) [
			'code'      => $this->get_code(),
			'name'      => $this->get_name(),
			'shortname' => $this->get_shortname(),
		];
	}
}
