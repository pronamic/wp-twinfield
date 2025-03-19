<?php
/**
 * REST office controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Offices\OfficesListRequest;
use Pronamic\WordPress\Twinfield\Offices\OfficesXmlReader;
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

		$xml_processor = $client->get_xml_processor();

		$offices_list_request = new OfficesListRequest();

		$offices_list_response = $xml_processor->process_xml_string( $offices_list_request->to_xml() );

		$offices_xml_reader = new OfficesXmlReader( $client->get_organisation() );

		$offices = $offices_xml_reader->read( $offices_list_response );

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
}
