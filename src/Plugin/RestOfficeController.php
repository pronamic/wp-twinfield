<?php
/**
 * REST office controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Offices\OfficeReadRequest;
use WP_REST_Request;
use WP_REST_Response;

/**
 * REST office controller class
 */
class RestOfficeController extends RestController {
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
			'/authorizations/(?P<post_id>\d+)/offices',
			[
				'methods'             => 'GET',
				'callback'            => $this->get_offices( ... ),
				'permission_callback' => $this->check_permission( ... ),
				'args'                => [
					'post_id' => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'embed'   => [
						'description' => 'Embed.',
						'type'        => 'string',
					],
					'pull'    => [
						'description' => 'Pull flag to update the local repository.',
						'type'        => 'boolean',
						'default'     => false,
						'required'    => false,
					],
				],
			]
		);

		register_rest_route(
			$namespace,
			'/authorizations/(?P<post_id>\d+)/offices/(?P<office_code>[a-zA-Z0-9_-]+)',
			[
				'methods'             => 'GET',
				'callback'            => $this->get_office( ... ),
				'permission_callback' => $this->check_permission( ... ),
				'args'                => [
					'post_id'     => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'office_code' => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
					/**
					 * Embed?
					 * 
					 * @link https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_embed
					 */
					'embed'       => [
						'description' => 'Embed.',
						'type'        => 'string',
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
		if ( \current_user_can( 'manage_options' ) ) {
			return true;
		}

		if ( \defined( 'WP_CLI' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get offices.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	private function get_offices( WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );

		$client = $this->plugin->get_client( $post );

		$office_service = $client->get_service( 'office' );

		$offices = $office_service->get_offices();

		/**
		 * Envelope.
		 * 
		 * @link https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_envelope
		 * @link https://jsonapi.org/format/#document-top-level
		 */
		$rest_response = new \WP_REST_Response(
			[
				'type'      => 'offices',
				'data'      => $offices,
				'_embedded' => (object) [
					'request'  => (string) $offices_list_request,
					'response' => (string) $offices_list_response,
				],
			]
		);

		$rest_response->add_link( 'self', \rest_url( $request->get_route() ) );

		$rest_response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					[
						':id' => $post->ID,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		return $rest_response;
	}

	/**
	 * Get office.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	private function get_office( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$office_code = $request->get_param( 'office_code' );

		$office = $organisation->office( $office_code );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$office_request = new OfficeReadRequest( $office_code );

		$office_response = $xml_processor->process_xml_string( $office_request->to_xml() );

		$office = \Pronamic\WordPress\Twinfield\Offices\Office::from_xml( (string) $office_response, $office );

		if ( $request->get_param( 'pull' ) ) {
			$orm = $this->plugin->get_orm();

			$organisation = $office->get_organisation();

			$organisation_id = $orm->first_or_create(
				$organisation,
				[
					'code' => $organisation->get_code(),
				],
				[],
			);

			$office_id = $orm->update_or_create(
				$office,
				[
					'organisation_id' => $organisation_id,
					'code'            => $office->get_code(),
				],
				[
					'xml' => (string) $office_response,
				]
			);
		}

		$data = [
			'type'      => 'office',
			'data'      => $office,
			'_embedded' => (object) [
				'request'  => $office_request->to_xml(),
				'response' => (string) $office_response,
			],
		];

		$response = new WP_REST_Response( $data );

		$response->add_link(
			'organisation',
			rest_url(
				strtr(
					'pronamic-twinfield/v1/authorizations/:id/organisation',
					[
						':id' => $post_id,
					]
				)
			),
			[
				'type'       => 'application/hal+json',
				'embeddable' => true,
			]
		);

		return $response;
	}
}
