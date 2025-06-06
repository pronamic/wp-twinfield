<?php
/**
 * Save hierarchy controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_CLI;
use WP_REST_Request;

/**
 * Save hierarchy controller class
 */
class SaveHierarchyController {
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
	 * Setup.
	 * 
	 * @return void
	 */
	public function setup() {
		\add_action( 'cli_init', $this->cli_init( ... ) );

		\add_action( 'pronamic_twinfield_save_hierarchies', $this->save_hierarchies( ... ) );
		\add_action( 'pronamic_twinfield_save_office_hierarchies', $this->save_office_hierarchies( ... ), 10, 2 );
		\add_action( 'pronamic_twinfield_save_office_hierarchy', $this->save_office_hierarchy( ... ), 10, 3 );
	}

	/**
	 * CLI intialize.
	 * 
	 * @return void
	 */
	public function cli_init() {
		/**
		 * Save office hierarchies.
		 * 
		 * Example:
		 * wp pronamic-twinfield save-hierarchies --authorization=5337
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-hierarchies',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				$this->save_hierarchies( $assoc_args['authorization'] );    
			}
		);

		/**
		 * Save office hierarchies.
		 * 
		 * Example:
		 * wp pronamic-twinfield save-office-hierarchies --authorization=5337 --office_code=1000
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-office-hierarchies',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				if ( ! \array_key_exists( 'office_code', $assoc_args ) ) {
					WP_CLI::error( 'Office code argument missing.' );
				}

				$this->save_office_hierarchies( $assoc_args['authorization'], $assoc_args['office_code'] );
			}
		);

		/**
		 * Save office hierarchy.
		 * 
		 * Example:
		 * wp pronamic-twinfield save-office-hierarchy --authorization=5337 --office_code=1000 --hierarchy_code=TWFRGS32
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-office-hierarchy',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				if ( ! \array_key_exists( 'office_code', $assoc_args ) ) {
					WP_CLI::error( 'Office code argument missing.' );
				}

				if ( ! \array_key_exists( 'hierarchy_code', $assoc_args ) ) {
					WP_CLI::error( 'Hierarchy code argument missing.' );
				}

				$this->save_office_hierarchy( $assoc_args['authorization'], $assoc_args['office_code'], $assoc_args['hierarchy_code'] );
			}
		);
	}

	/**
	 * Save hierarchies.
	 * 
	 * @param string|int $authorization Authorization.
	 * @return void
	 */
	private function save_hierarchies( $authorization ) {
		global $wpdb;

		$request = new WP_REST_Request( 'GET', '/pronamic-twinfield/v1/authorizations/' . $authorization . '/offices' );

		$request->set_param( 'authorization', $authorization );

		$response = \rest_do_request( $request );

		$data = (object) $response->get_data();

		/**
		 * Template offices.
		 * 
		 * Bank statements cannot be requested from template administrations.
		 */
		$offices_table = $wpdb->prefix . 'twinfield_offices';

		$codes = $wpdb->get_col( "SELECT code FROM $offices_table WHERE is_template = TRUE;" );

		$offices = $data->data;

		$offices = \array_filter(
			$offices,
			fn( $office ) => ! \in_array( $office->get_code(), $codes, true )
		);

		foreach ( $offices as $office ) {
			$office_code = $office->get_code();

			$action_id = \as_enqueue_async_action(
				'pronamic_twinfield_save_office_hierarchies',
				[
					'authorization' => $authorization,
					'office_code'   => $office_code,
				],
				'pronamic-twinfield'
			);

			$this->log(
				\sprintf(
					'Saving administration hierarchies is scheduled, authorization post ID: %s, office code: %s, action ID: %s.',
					$authorization,
					$office_code,
					$action_id
				)
			);
		}
	}

	/**
	 * Save office hierarchies.
	 * 
	 * @param string|int $authorization Authorization.
	 * @param string     $office_code   Office code.
	 * @return void
	 */
	private function save_office_hierarchies( $authorization, $office_code ) {
		$client = $this->plugin->get_client( \get_post( $authorization ) );

		$organisation = $client->get_organisation();

		$office = $organisation->office( $office_code );

		$hierarchy_service = $client->get_service( 'hierarchies' );

		$response = $hierarchy_service->search_hierarchies( $office );

		$hierarchy_codes = $response->to_hierarchy_codes();

		foreach ( $hierarchy_codes as $hierarchy_code ) {
			$hierarchy_code = (string) $hierarchy_code;

			$action_id = \as_enqueue_async_action(
				'pronamic_twinfield_save_office_hierarchy',
				[
					'authorization'  => $authorization,
					'office_code'    => $office_code,
					'hierarchy_code' => $hierarchy_code,
				],
				'pronamic-twinfield'
			);

			$this->log(
				\sprintf(
					'Saving administration hierarchy is scheduled, authorization post ID: %s, office code: %s, hierarchy code: %s, action ID: %s.',
					$authorization,
					$office_code,
					$hierarchy_code,
					$action_id
				)
			);
		}
	}

	/**
	 * Save office hierarchies.
	 * 
	 * @param string|int $authorization  Authorization.
	 * @param string     $office_code    Office code.
	 * @param string     $hierarchy_code Hierarchy code.
	 * @return void
	 */
	private function save_office_hierarchy( $authorization, $office_code, $hierarchy_code ) {
		$client = $this->plugin->get_client( \get_post( $authorization ) );

		$organisation = $client->get_organisation();

		$office = $organisation->office( $office_code );

		$hierarchy_service = $client->get_service( 'hierarchies' );

		$hierarchy_load_response = $hierarchy_service->get_hierarchy( $office, $hierarchy_code );

		$hierarchy = $hierarchy_load_response->hierarchy;

		$this->log( \wp_json_encode( $hierarchy_load_response ) );

		$orm = $this->plugin->get_orm();

		$organisation_id = $orm->first_or_create(
			$organisation,
			[
				'code' => $organisation->get_code(),
			],
			[],
		);

		$office_id = $orm->first_or_create(
			$office,
			[
				'organisation_id' => $organisation_id,
				'code'            => $office->get_code(),
			],
			[]
		);

		$hierarchy_id = $orm->update_or_create(
			$hierarchy,
			[
				'office_id' => $office_id,
				'code'      => $hierarchy->code,
			],
			[
				'name'        => $hierarchy->name,
				'description' => $hierarchy->description,
				'json'        => \wp_json_encode( $hierarchy ),
			]
		);
	}

	/**
	 * Log.
	 * 
	 * @param string $message Message.
	 * @return void
	 */
	private function log( $message ) {
		if ( \method_exists( WP_CLI::class, 'log' ) ) {
			WP_CLI::log( $message );
		}
	}
}
