<?php
/**
 * REST periods controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_REST_Request;
use WP_REST_Response;

/**
 * REST periods controller class
 */
class RestPeriodsController extends RestController {
	/**
	 * REST API initialize.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @return void
	 */
	public function rest_api_init() {
		$namespace = 'pronamic-twinfield/v1';

		register_rest_route(
			$namespace,
			'/offices/(?P<office_code>[a-zA-Z0-9_-]+)/periods',
			[
				'methods'             => 'GET',
				'callback'            => $this->get_periods( ... ),
				'permission_callback' => $this->check_permission( ... ),
				'args'                => [
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
					'years' => [
						'description' => 'Years.',
						'type'        => 'array',
						'items'       => [
							'type' => 'integer',
						],
					],
				],
			]
		);
	}

	/**
	 * Check permission.
	 *
	 * @return bool True if permission, false otherwise.
	 */
	private function check_permission() {
		if ( \current_user_can( 'pronamic_twinfield_read_periods' ) ) {
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
	 * REST API periods.
	 *
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	private function get_periods( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = \get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$periods_service = $client->get_service( 'periods' );

		$periods_service->set_office( $office );

		$years = $request->get_param( 'years' );

		$periods = [];

		foreach ( $years as $year ) {
			try {
				$year_periods = $periods_service->get_periods( $office, $year );
			} catch ( \Pronamic\WordPress\Twinfield\Periods\YearNotFoundException ) {
				continue;
			}

			foreach ( $year_periods as $period ) {
				$periods[] = $period;
			}
		}

		return $periods;
	}
}
