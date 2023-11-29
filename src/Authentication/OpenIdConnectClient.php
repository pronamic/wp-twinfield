<?php
/**
 * OpenID Connect Client
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Authentication;

use Pronamic\WordPress\Http\Facades\Http;

/**
 * OpenID Connect Client
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 * @see        https://github.com/opauth/opauth
 */
class OpenIdConnectClient {
	/**
	 * Authorize URL.
	 *
	 * @var string
	 */
	public const URL_AUTHORIZE = 'https://login.twinfield.com/auth/authentication/connect/authorize';

	/**
	 * Token URL.
	 *
	 * @var string
	 */
	public const URL_TOKEN = 'https://login.twinfield.com/auth/authentication/connect/token';

	/**
	 * Access token validation URL.
	 *
	 * @var string
	 */
	public const URL_ACCESS_TOKEN_VALIDATION = 'https://login.twinfield.com/auth/authentication/connect/accesstokenvalidation';

	/**
	 * User info URL.
	 *
	 * @var string
	 */
	public const URL_USER_INFO = 'https://login.twinfield.com/auth/authentication/connect/userinfo';

	/**
	 * Construct.
	 *
	 * @param string $client_id     Client ID.
	 * @param string $client_secret Client secret.
	 * @param string $redirect_uri  Redirect URI.
	 */
	public function __construct( $client_id, $client_secret, $redirect_uri ) {
		$this->client_id     = $client_id;
		$this->client_secret = $client_secret;
		$this->redirect_uri  = $redirect_uri;
	}


	/**
	 * Get authorization header.
	 *
	 * @see https://developer.wordpress.org/plugins/http-api/#get-using-basic-authentication
	 * @see https://c3.twinfield.com/webservices/documentation/#/ApiReference/Authentication/OpenIdConnect#General-information
	 * @return string
	 */
	private function get_authorization_header() {
		return 'Basic ' . base64_encode( $this->client_id . ':' . $this->client_secret );
	}

	/**
	 * Get headers.
	 *
	 * @return array
	 */
	private function get_headers() {
		return [
			'Authorization' => $this->get_authorization_header(),
		];
	}

	/**
	 * Set state.
	 *
	 * @param string $state State.
	 * @return void
	 */
	public function set_state( $state ) {
		$this->state = $state;
	}

	/**
	 * Get authorize URL.
	 *
	 * @return string
	 */
	public function get_authorize_url() {
		$url = self::URL_AUTHORIZE;

		$args = [
			'client_id'     => $this->client_id,
			'response_type' => 'code',
			'scope'         => implode(
				'+',
				[
					'openid',
					'twf.user',
					'twf.organisation',
					'twf.organisationUser',
					'offline_access',
				]
			),
			'redirect_uri'  => $this->redirect_uri,
			'nonce'         => wp_create_nonce( 'twinfield-auth' ),
		];

		/**
		 * State.
		 *
		 * @link https://auth0.com/docs/protocols/oauth2/oauth-state
		 */
		if ( null !== $this->state ) {
			$args['state'] = \rawurlencode( $this->state );
		}

		$url = add_query_arg( $args, $url );

		return $url;
	}

	/**
	 * Get access token.
	 *
	 * @param string $code Code.
	 * @return string
	 * @throws \Exception Throws exception when access token could not be retrieved.
	 */
	public function get_access_token( $code ) {
		$url = self::URL_TOKEN;

		$result = Http::post(
			$url,
			[
				'headers' => $this->get_headers(),
				'body'    => [
					'grant_type'   => 'authorization_code',
					'code'         => $code,
					'redirect_uri' => $this->redirect_uri,
				],
			]
		);

		$data = $result->json();

		if ( ! \is_object( $data ) ) {
			throw new \Exception(
				\sprintf(
					'Unknow response from `%s` endpoint.',
					\esc_html( $url )
				)
			);
		}

		if ( \property_exists( $data, 'error' ) ) {
			throw new \Exception(
				\sprintf(
					'Received error from `%s` endpoint: %s.',
					\esc_html( $url ),
					\esc_html( $data->error )
				)
			);
		}

		return $data;
	}

	/**
	 * Get access token validation.
	 *
	 * @param string $access_token Access token.
	 * @return mixed
	 * @throws \Exception Throws exception when access token validation could not be retrieved.
	 */
	public function get_access_token_validation( $access_token ) {
		$url = self::URL_ACCESS_TOKEN_VALIDATION;
		$url = \add_query_arg( 'token', $access_token, $url );

		$result = Http::get( $url );

		$data = $result->json();

		if ( ! \is_object( $data ) ) {
			throw new \Exception(
				\sprintf(
					'Unknow response from `%s` endpoint.',
					\esc_html( $url )
				)
			);
		}

		if ( \property_exists( $data, 'Message' ) ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			throw new \Exception( \esc_html( $data->Message ) );
		}

		return $data;
	}

	/**
	 * Refresh token.
	 *
	 * @param string $refresh_token Refresh token.
	 * @return mixed
	 * @throws \Exception Throws exception when token could not be refreshed.
	 */
	public function refresh_token( $refresh_token ) {
		$url = self::URL_TOKEN;

		$result = Http::post(
			$url,
			[
				'headers' => $this->get_headers(),
				'body'    => [
					'grant_type'    => 'refresh_token',
					'refresh_token' => $refresh_token,
				],
			]
		);

		$data = $result->json();

		if ( ! \is_object( $data ) ) {
			throw new \Exception(
				\sprintf(
					'Unknow response from `%s` endpoint.',
					\esc_html( $url )
				)
			);
		}

		return $data;
	}

	/**
	 * Get user info.
	 *
	 * ```
	 * curl --header "Authorization: Bearer ●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●●" https://login.twinfield.com/auth/authentication/connect/userinfo
	 * ```
	 *
	 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Authentication/OpenIdConnect
	 * @link https://connect2id.com/products/server/docs/api/userinfo
	 * @param string $access_token Access token.
	 * @return object
	 */
	public function get_user_info( $access_token ) {
		$url = self::URL_USER_INFO;

		$result = Http::get(
			$url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $access_token,
				],
			]
		);

		$data = $result->json();

		return $data;
	}

	/**
	 * From JSON file.
	 * 
	 * @param string $file File.
	 * @return self
	 */
	public static function from_json_file( $file ) {
		// phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$data = \json_decode( \file_get_contents( $file, true ) );

		$client = new self( $data->client_id, $data->client_secret, $data->redirect_uri );

		return $client;
	}
}
