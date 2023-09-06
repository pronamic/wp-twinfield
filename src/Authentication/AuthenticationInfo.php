<?php
/**
 * Authentication info.
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Authentication;

use JsonSerializable;

/**
 * Authentication info.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class AuthenticationInfo implements JsonSerializable {
	/**
	 * Tokens.
	 *
	 * @var AuthenticationTokens
	 */
	private $tokens;

	/**
	 * Validation response.
	 *
	 * @var AccessTokenValidation
	 */
	private $validation;

	/**
	 * Construct an authentication info object.
	 *
	 * @param AuthenticationTokens  $tokens Tokens.
	 * @param AccessTokenValidation $validation Validation.
	 */
	public function __construct( AuthenticationTokens $tokens, AccessTokenValidation $validation ) {
		$this->tokens     = $tokens;
		$this->validation = $validation;
	}

	/**
	 * Tokens.
	 * 
	 * @return AuthenticationTokens
	 */
	public function get_tokens() {
		return $this->tokens;
	}

	/**
	 * Validation.
	 * 
	 * @return AccessTokenValidation
	 */
	public function get_validation() {
		return $this->validation;
	}

	/**
	 * JSON serialize.
	 * 
	 * @return object
	 */
	public function jsonSerialize() {
		return (object) [
			'tokens'     => $this->tokens->jsonSerialize(),
			'validation' => $this->validation->jsonSerialize(),
		];
	}

	/**
	 * Create access token validation object from a plain object.
	 * 
	 * @param object $object Object.
	 * @return self
	 */
	public static function from_object( $object ) {
		return new self(
			AuthenticationTokens::from_object( $object->tokens ),
			AccessTokenValidation::from_object( $object->validation )
		);
	}
}
