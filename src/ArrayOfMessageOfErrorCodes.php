<?php
/**
 * Array of message of error codes
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use IteratorAggregate;
use JsonSerializable;


/**
 * Array of message of error codes
 *
 * This class represents the 'ArrayOfMessageOfErrorCodes' Twinfield class.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ArrayOfMessageOfErrorCodes implements IteratorAggregate, JsonSerializable {
	/**
	 * An array with message of error codes.
	 *
	 * @var mixed
	 */
	private $MessageOfErrorCodes; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * Constructs and initializes an array of array of string object.
	 */
	public function __construct() {
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.
		$this->MessageOfErrorCodes = [];
	}

	/**
	 * If there is only one error the `MessageOfErrorCodes` variable is not an array.
	 * This function will correct this when needed.
	 */
	public function get_array() {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.

		if ( is_array( $this->MessageOfErrorCodes ) ) {
			return $this->MessageOfErrorCodes;
		} else {
			if ( $this->MessageOfErrorCodes instanceof MessageOfErrorCodes ) {
				return [ $this->MessageOfErrorCodes ];
			} else {
				return [];
			}
		}

		// phpcs:enable
	}

	/**
	 * Add the specified array of string to this object.
	 *
	 * @param MessageOfErrorCodes $error Add the specified array.
	 */
	public function add( MessageOfErrorCodes $error ) {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.

		$this->MessageOfErrorCodes = $this->get_array();

		$this->MessageOfErrorCodes[] = $error;

		// phpcs:enable
	}

	/**
	 * Get iterator.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->get_array() );
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return $this->get_array();
	}
}
