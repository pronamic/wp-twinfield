<?php
/**
 * REST hierarchy controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_REST_Request;
use WP_REST_Response;

/**
 * REST hierarchy controller class
 */
class RestHierarchyController extends RestController {
	/**
	 * REST API initialize.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @return void
	 */
	public function rest_api_init() {
		$namespace = 'pronamic-twinfield/v1';

		$hierarchies_args = [
			'post_id'        => [
				'description'       => 'Authorization post ID.',
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => \get_option( 'pronamic_twinfield_authorization_post_id' ),
			],
			'office_code'    => [
				'description' => 'Twinfield office code.',
				'type'        => 'string',
			],
			'hierarchy_code' => [
				'description' => 'Twinfield hierarchy code.',
				'type'        => 'string',
			],
		];

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)/hierarchies/(?P<hierarchy_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_hierarchy' ],
				'permission_callback' => $this->permission_callback( ... ),
				'args'                => $hierarchies_args,
			]
		);

		register_rest_route(
			$namespace,
			'/offices/(?P<office_code>[a-zA-Z0-9_-]+)/hierarchies/(?P<hierarchy_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_api_hierarchy' ],
				'permission_callback' => $this->permission_callback( ... ),
				'args'                => $hierarchies_args,
			]
		);
	}

	/**
	 * Permission callback.
	 * 
	 * @return bool True if permission, false otherwise.
	 */
	public function permission_callback() {
		if ( \current_user_can( 'pronamic_twinfield_read_hierarchies' ) ) {
			return true;
		}

		if ( \current_user_can( 'manage_options' ) ) {
			return true;
		}

		if ( \defined( 'WP_CLI' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * REST API hierarchy.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_hierarchy( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$hierarchies_service = $client->get_service( 'hierarchies' );

		$hierarchies_service->set_office( $office );

		$hierarchy_code = $request->get_param( 'hierarchy_code' );

		$hierarchy = $hierarchies_service->get_hierarchy( $hierarchy_code );

		return $hierarchy;
	}
}
