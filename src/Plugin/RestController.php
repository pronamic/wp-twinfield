<?php
/**
 * REST controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_REST_Request;
use WP_REST_Response;

/**
 * REST controller class
 */
class RestController {
	/**
	 * Plugin.
	 * 
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Construct REST controller.
	 * 
	 * @param Plugin $plugin Plugin.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * REST API arguments.
	 *
	 * @return array
	 */
	protected function get_authorization_schema() {
		return [
			'description' => \__( 'Authorization.', 'pronamic' ),
			'type'        => [ 'integer', 'string' ],
			'required'    => false,
		];
	}

	/**
	 * Handle authorization.
	 * 
	 * @link https://github.com/wp-cli/wp-cli/blob/c651e20c00096b4c7fb7543dfa7559ed0667e7dc/php/WP_CLI/Runner.php#L1608-L1624
	 * @param WP_REST_Request $request WordPress REST request object.
	 * @return WP_Post
	 */
	protected function handle_authorization( WP_REST_Request $request ) {
		$authorization = (string) \get_option( 'pronamic_twinfield_authorization_post_id' );

		if ( $request->has_param( 'authorization' ) ) {
			$authorization = $request->get_param( 'authorization' );
		}

		$authorization_post = null;

		if ( is_numeric( $authorization ) ) {
			$authorization_post = \get_post( (int) $authorization );
		}

		if ( null === $authorization_post ) {
			$authorization_post = \get_page_by_path( $authorization, OBJECT, 'pronamic_twf_auth' );
		}

		return $authorization_post;
	}
}
