<?php
/**
 * Authentication Tokens.
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Authentication;

use DateTimeInterface;
use DateTimeImmutable;
use JsonSerializable;

/**
 * Authentication Tokens.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class AuthenticationTokens implements JsonSerializable {
	/**
	 * Access Token.
	 *
	 * @var string
	 */
	private $access_token;

	/**
	 * Token Type.
	 *
	 * @var string
	 */
	private $token_type;

	/**
	 * Refresh Token.
	 *
	 * @var string
	 */
	private $refresh_token;

	/**
	 * Construct access token.
	 *
	 * @param string $access_token  Access token.
	 * @param string $token_type    Token type.
	 * @param string $refresh_token Refresh token.
	 */
	public function __construct( $access_token, $token_type, $refresh_token ) {
		$this->access_token  = $access_token;
		$this->token_type    = $token_type;
		$this->refresh_token = $refresh_token;
	}

	/**
	 * Get access token.
	 *
	 * @return string
	 */
	public function get_access_token() {
		return $this->access_token;
	}

	/**
	 * Get refresh token.
	 *
	 * @return string
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return [
			'access_token'  => $this->access_token,
			'token_type'    => $this->token_type,
			'refresh_token' => $this->refresh_token,
		];
	}

	/**
	 * Create access token validation object from a plain object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_object( $value ) {
		return new self( $value->access_token, $value->token_type, $value->refresh_token );
	}
}
