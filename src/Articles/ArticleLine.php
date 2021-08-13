<?php
/**
 * Article line
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Articles;

/**
 * Article line
 *
 * This class represents an Twinfield article line.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ArticleLine {
	/**
	 * Name
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Constructs and initialize an Twinfield article line.
	 */
	public function __construct() {

	}

	/**
	 * Set name.
	 *
	 * @param string $name The name.
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}
}
