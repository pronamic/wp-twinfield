<?php
/**
 * Load Response
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Load Response
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
final class HierarchyLoadResponse implements JsonSerializable {
	/**
	 * Hierarchy.
	 *
	 * @var Hierarchy
	 */
	public Hierarchy $hierarchy;

	/**
	 * Construct load response.
	 *
	 * @param Hierarchy $hierarchy Hierarchy.
	 */
	public function __construct( Hierarchy $hierarchy ) {
		$this->hierarchy = $hierarchy;
	}

	/**
	 * Get hierarchy.
	 *
	 * @return Hierarchy
	 */
	public function get_hierarchy() {
		return $this->hierarchy;
	}

	/**
	 * Convert from Twinfield object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$hierarchy = Hierarchy::from_twinfield_object( $data->get_property( 'hierarchy' ) );

		$load_response = new self( $hierarchy );

		return $load_response;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'hierarchy' => $this->hierarchy,
		];
	}
}
