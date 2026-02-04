<?php
/**
 * Message of error codes
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use JsonSerializable;
use Pronamic\WordPress\Twinfield\Exceptions\AccessDeniedException;
use Pronamic\WordPress\Twinfield\Exceptions\InvalidFinderTypeException;
use Pronamic\WordPress\Twinfield\Exceptions\NoAccessToOfficeException;
use Pronamic\WordPress\Twinfield\Exceptions\OptionNotAllowedException;

/**
 * Message of error codes
 *
 * This class represents a Twinfield message with error codes.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class MessageOfErrorCodes implements JsonSerializable {
	/**
	 * The message type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The message text.
	 *
	 * @var string|null
	 */
	private $text;

	/**
	 * The error code.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * The parameters.
	 *
	 * @var array|null
	 */
	private $parameters;

	/**
	 * Construct message of error codes.
	 *
	 * @param string      $type       Message type (Error, Warning, Informational).
	 * @param string|null $text       Message text.
	 * @param string      $code       Error code.
	 * @param array|null  $parameters Parameters.
	 */
	public function __construct( $type, $text, $code, $parameters = null ) {
		$this->type       = $type;
		$this->text       = $text;
		$this->code       = $code;
		$this->parameters = $parameters;
	}

	/**
	 * Get the message type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get the message text.
	 *
	 * @return string|null
	 */
	public function get_text() {
		return $this->text;
	}

	/**
	 * Get the error code.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Get the parameters.
	 *
	 * @return array|null
	 */
	public function get_parameters() {
		return $this->parameters;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'type'       => $this->type,
			'text'       => $this->text,
			'code'       => $this->code,
			'parameters' => $this->parameters,
		];
	}

	/**
	 * Convert to appropriate exception.
	 *
	 * @param \Throwable|null $previous Previous exception for chaining.
	 * @return TwinfieldException
	 */
	public function to_exception( ?\Throwable $previous = null ): TwinfieldException {
		$message = sprintf(
			'[%s] %s (Code: %s)',
			$this->type,
			$this->text ?? 'Unknown error',
			$this->code
		);

		return match ( $this->code ) {
			'AccessDenied' => new AccessDeniedException( $message, $this->code, $this->type, $this->parameters, $previous ),
			'NoAccessToOffice' => new NoAccessToOfficeException( $message, $this->code, $this->type, $this->parameters, $previous ),
			'OptionNotAllowed' => new OptionNotAllowedException( $message, $this->code, $this->type, $this->parameters, $previous ),
			'InvalidFinderType' => new InvalidFinderTypeException( $message, $this->code, $this->type, $this->parameters, $previous ),
			default => new TwinfieldException( $message, $this->code, $this->type, $this->parameters, $previous ),
		};
	}

	/**
	 * From Twinfield object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		return new self(
			$value->Type ?? '',
			$value->Text ?? null,
			$value->Code ?? '',
			isset( $value->Parameters->string ) ? (array) $value->Parameters->string : null
		);
	}
}
