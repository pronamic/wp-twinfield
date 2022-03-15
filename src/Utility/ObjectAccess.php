<?php
/**
 * Object access
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Utility
 */

namespace Pronamic\WordPress\Twinfield\Utility;

/**
 * Object access
 *
 * This class represents a Twinfield transaction type code.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ObjectAccess {
	/**
	 * Construct object access.
	 * 
	 * @param object $object Object.
	 * @throws \InvalidArgumentException Throws invalid argument exception when object is not an object.
	 */
	public function __construct( $object ) {
		if ( ! \is_object( $object ) ) {
			throw new \InvalidArgumentException( 'Object access can only handle objects.' );
		}

		$this->object = $object;
	}

	/**
	 * Get property.
	 * 
	 * @param string $property Property.
	 * @return mixed
	 * @throws \Exception Throws exception when property does not exists.
	 */
	public function get_property( $property ) {
		if ( ! \property_exists( $this->object, $property ) ) {
			throw new \Exception( \sprintf( 'Object does not have `%s` property.', $property ) );
		}

		return $this->object->{$property};
	}

	/**
	 * Get object.
	 * 
	 * @param string $property Property.
	 * @return self
	 */
	public function get_object( $property ) {
		return self::from_object( $this->get_property( $property ) );
	}

	/**
	 * Get array.
	 * 
	 * @param string $property Property.
	 * @return array
	 */
	public function get_array( $property ) {
		$value = $this->get_property( $property );

		if ( \is_array( $value ) ) {
			return $value;
		}

		return [ $value ];
	}

	/**
	 * Create object access object from object.
	 * 
	 * @param object $object Object.
	 * @return self
	 */
	public static function from_object( $object ) {
		return new self( $object );
	}
}
