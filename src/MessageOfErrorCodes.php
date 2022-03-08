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

/**
 * Message of error codes
 *
 * This class represents the 'MessageOfErrorCodes' Twinfield class.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class MessageOfErrorCodes implements JsonSerializable {
	/**
	 * Type of error.
	 *
	 * @var string
	 */
	private $Type; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * Text of error
	 *
	 * @var string
	 */
	private $Text; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * Code of error
	 *
	 * @var string
	 */
	private $Code; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * Parameters of error
	 *
	 * @var ArrayOfString
	 */
	private $Parameters; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'type'       => $this->Type,
			'text'       => $this->Text,
			'code'       => $this->Code,
			'parameters' => $this->Parameters,
		];
	}
}
