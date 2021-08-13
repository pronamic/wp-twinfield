<?php
/**
 * Code name
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Code name
 *
 * This class represents a Twinfield code and name.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CodeName {
	/**
	 * Code.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * Name.
	 *
	 * @var string|null
	 */
	private $name;

	/**
	 * Shortname.
	 *
	 * @var string|null
	 */
	private $shortname;

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
}
