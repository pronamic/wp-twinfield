<?php
/**
 * Array of message of error codes
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;

/**
 * Array of message of error codes
 *
 * This class represents an array of Twinfield messages with error codes.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ArrayOfMessageOfErrorCodes implements IteratorAggregate, JsonSerializable {
	/**
	 * The array of messages.
	 *
	 * @var array
	 */
	private $messages;

	/**
	 * Construct array of message of error codes.
	 *
	 * @param array $messages Array of MessageOfErrorCodes objects.
	 */
	public function __construct( $messages = [] ) {
		$this->messages = $messages;
	}

	/**
	 * Get the array of messages.
	 *
	 * @return array
	 */
	public function get_array() {
		return $this->messages;
	}

	/**
	 * Add a message.
	 *
	 * @param MessageOfErrorCodes $message Message to add.
	 * @return void
	 */
	public function add( MessageOfErrorCodes $message ) {
		$this->messages[] = $message;
	}

	/**
	 * Check if array is empty.
	 *
	 * @return bool
	 */
	public function is_empty() {
		return empty( $this->messages );
	}

	/**
	 * Throw exception if there are errors.
	 *
	 * @return void
	 * @throws TwinfieldException If there are error messages.
	 */
	public function throw_if_error() {
		if ( $this->is_empty() ) {
			return;
		}

		$exception = null;

		foreach ( $this->messages as $message ) {
			$exception = $message->to_exception( $exception );
		}

		throw $exception;
	}

	/**
	 * Get iterator.
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator( $this->messages );
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return $this->messages;
	}

	/**
	 * From Twinfield object.
	 *
	 * @param object|null $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		$messages = [];

		if ( isset( $value->MessageOfErrorCodes ) ) {
			$items = is_array( $value->MessageOfErrorCodes )
				? $value->MessageOfErrorCodes
				: [ $value->MessageOfErrorCodes ];

			foreach ( $items as $item ) {
				$messages[] = MessageOfErrorCodes::from_twinfield_object( $item );
			}
		}

		return new self( $messages );
	}
}
