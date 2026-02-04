<?php
/**
 * Twinfield Exception
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Twinfield Exception class
 *
 * Base exception for Twinfield API errors.
 *
 * @package Pronamic/WordPress/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class TwinfieldException extends \RuntimeException {
	/**
	 * The error code from Twinfield.
	 *
	 * @var string
	 */
	private $error_code;

	/**
	 * The message type.
	 *
	 * @var string
	 */
	private $message_type;

	/**
	 * The parameters.
	 *
	 * @var array|null
	 */
	private $parameters;

	/**
	 * Construct Twinfield exception.
	 *
	 * @param string          $message      Exception message.
	 * @param string          $error_code   Twinfield error code.
	 * @param string          $message_type Message type (Error, Warning, Informational).
	 * @param array|null      $parameters   Optional parameters.
	 * @param \Throwable|null $previous     Previous exception.
	 */
	public function __construct( string $message, string $error_code, string $message_type, ?array $parameters = null, ?\Throwable $previous = null ) {
		parent::__construct( $message, 0, $previous );

		$this->error_code   = $error_code;
		$this->message_type = $message_type;
		$this->parameters   = $parameters;
	}

	/**
	 * Get the Twinfield error code.
	 *
	 * @return string
	 */
	public function get_error_code(): string {
		return $this->error_code;
	}

	/**
	 * Get the message type.
	 *
	 * @return string
	 */
	public function get_message_type(): string {
		return $this->message_type;
	}

	/**
	 * Get the parameters.
	 *
	 * @return array|null
	 */
	public function get_parameters(): ?array {
		return $this->parameters;
	}
}
