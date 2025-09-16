<?php
/**
 * REST finder controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Finder\Search;
use WP_REST_Request;
use WP_REST_Response;

/**
 * REST finder controller class
 */
class RestFinderController extends RestController {
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
			'/finder',
			[
				'methods'             => 'GET',
				'callback'            => $this->search( ... ),
				'permission_callback' => $this->check_permission( ... ),
				'args'                => [
					'post_id'        => [
						'description'       => 'Authorization post ID.',
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'default'           => \get_option( 'pronamic_twinfield_authorization_post_id' ),
					],
					'type'    => [
						'description' => 'Finder type.',
						'type'        => 'string',
						'required'    => true,
						'enum'        => [
							'ART',
							'ASM',
							'BDS',
							'BNK',
							'BSN',
							'CDA',
							'CER',
							'CQT',
							'CTR',
							'CUR',
							'DIM',
							'DMT',
							'DVT',
							'FLT',
							'FMT',
							'GRP',
							'GWY',
							'HIE',
							'HND',
							'ICTCOUNTRY',
							'INV',
							'IVT',
							'MAT',
							'OFF',
							'OFG',
							'OIC',
							'PAY',
							'PIS',
							'PRD',
							'REP',
							'REW',
							'RMD',
							'ROL',
							'SAR',
							'SPM',
							'TXG',
							'TEQ',
							'TRS',
							'TRT',
							'USR',
							'VAT',
							'VATN',
							'VTB',
							'VGM',
							'XLT',
						],
					],
					'pattern' => [
						'description' => 'The search pattern. May contain wildcards * and ?.',
						'type'        => 'string',
						'required'    => true,
						'default'     => '*',
					],
					'field' => [
						'description' => 'Fields to search through, see Search fields.',
						'type'        => 'int',
						'required'    => true,
						'default'     => 0,
					],
					'first_row' => [
						'description' => 'First row to return, usefull for paging.',
						'type'        => 'int',
						'required'    => true,
						'default'     => 1,
					],
					'max_rows' => [
						'description' => 'Maximum number of rows to return, usefull for paging.',
						'type'        => 'int',
						'required'    => true,
						'default'     => 10,
					],
					/**
					 * Finder options.
					 *
					 * @link https://developers.twinfield.com/documentation/api/miscellaneous/finder/#finder-options
					 */
					'options' => [
						'description' => 'The Finder options.',
						'type'        => 'object',
						'properties'  => [
							/**
							 * Office.
							 */
							'includeid' => [
								'description' => 'Include ID.',
								'type'        => 'string',
								'enum'        => [
									'0',
									'1',
								],
							],
							'withouttaxgroup' => [
								'description' => 'Without taxgroup.',
								'type'        => 'string',
								'enum'        => [
									'0',
									'1',
								],
							],
						]
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
	 * REST API search.
	 *
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	private function search( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'post_id' );

		$post = \get_post( $post_id );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$finder = $client->get_finder();

		$type      = $request->get_param( 'type' );
		$pattern   = $request->get_param( 'pattern' );
		$field     = $request->get_param( 'field' );
		$first_row = $request->get_param( 'first_row' );
		$max_rows  = $request->get_param( 'max_rows' );

		$options = $request->get_param( 'options' ) ?? [];

		/**
		 * Office.
		 *
		 * Since it is not possible to add the company code
		 * to the finder, make sure the correct company is
		 * set by using either the SelectCompany function
		 * or adding the office option.
		 *
		 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Miscellaneous/Finder
		 */
		$office_code = $request->get_param( 'office_code' );

		if ( ! empty( $office_code ) ) {
			$office = $organisation->office( $office_code );

			$finder->set_office( $office );

			$options['office'] = $office_code;
		}

		/**
		 * Dimension type.
		 */
		$dimension_type = $request->get_param( 'dimtype' );

		if ( ! empty( $dimension_type ) ) {
			$options['dimtype'] = $dimension_type;
		}

		$search = new Search( $type, $pattern, $field, $first_row, $max_rows, $options );

		$response = $finder->search( $search );

		return $response;
	}
}
