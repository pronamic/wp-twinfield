<?php
/**
 * REST process XML controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_REST_Request;
use WP_REST_Response;

/**
 * REST process XML controller class
 */
class RestProcessXmlController extends RestController {
	/**
	 * REST API initialize.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @return void
	 */
	public function rest_api_init() {
		$namespace = 'pronamic-twinfield/v1';

		\register_rest_route(
			$namespace,
			'process-xml',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'rest_api_process_xml' ],
				'permission_callback' => function() {
					return true;
				},
				'args'                => [
					'xml'    => [
						'description' => \__( 'XML.', 'pronamic-twinfield' ),
						'type'        => 'string',
					],
					'office_code' => [
						'description' => 'Twinfield office code.',
						'type'        => 'string',
					],
				]
			]
		);
	}

	/**
	 * REST API process XML.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_process_xml( WP_REST_Request $request ) {
		$xml = $request->get_param( 'xml' );

		$post = get_post( \get_option( 'pronamic_twinfield_authorization_post_id' ) );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$xml_processor = $client->get_xml_processor();

		// Office.
		$office_code = $request->get_param( 'office_code' );

		if ( ! empty( $office_code ) ) {
			$office = $organisation->office( $office_code );

			$xml_processor->set_office( $office );
		}

		// Request.
		$response = $xml_processor->process_xml_string( $xml );

		/**
		 * Envelope.
		 * 
		 * @link https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_envelope
		 * @link https://jsonapi.org/format/#document-top-level
		 */
		$rest_response = new WP_REST_Response(
			[
				'_embedded' => (object) [
					'request'  => (string) $xml,
					'response' => (string) $response,
				],
			]
		);

		return $rest_response;
	}
}
