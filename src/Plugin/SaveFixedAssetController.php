<?php
/**
 * Save fixed asset controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_CLI;
use WP_REST_Request;

/**
 * Save fixed asset controller class
 */
class SaveFixedAssetController {
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

		\add_action( 'pronamic_twinfield_save_fixed_assets', $this->save_fixed_assets( ... ) );
		\add_action( 'pronamic_twinfield_save_office_fixed_assets', $this->save_office_fixed_assets( ... ), 10, 2 );
	}

	/**
	 * CLI intialize.
	 *
	 * @return void
	 */
	public function cli_init() {
		/**
		 * Save fixed assets.
		 *
		 * Example:
		 * wp pronamic-twinfield save-fixed-assets --authorization=5337
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-fixed-assets',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				$this->save_fixed_assets( $assoc_args['authorization'] );
			}
		);

		/**
		 * Save office fixed assets.
		 *
		 * Example:
		 * wp pronamic-twinfield save-office-fixed-assets --authorization=5337 --office_code=1000
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-office-fixed-assets',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				if ( ! \array_key_exists( 'office_code', $assoc_args ) ) {
					WP_CLI::error( 'Office code argument missing.' );
				}

				$this->save_office_fixed_assets( $assoc_args['authorization'], $assoc_args['office_code'] );
			}
		);
	}

	/**
	 * Save fixed assets.
	 *
	 * @param string|int $authorization Authorization.
	 * @return void
	 */
	private function save_fixed_assets( $authorization ) {
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
				'pronamic_twinfield_save_office_fixed_assets',
				[
					'authorization' => $authorization,
					'office_code'   => $office_code,
				],
				'pronamic-twinfield'
			);

			$this->log(
				\sprintf(
					'Saving administration fixed assets is scheduled, authorization post ID: %s, office code: %s, action ID: %s.',
					$authorization,
					$office_code,
					$action_id
				)
			);
		}
	}

	/**
	 * Save office fixed assets.
	 *
	 * @param string|int $authorization Authorization.
	 * @param string     $office_code   Office code.
	 * @return void
	 */
	private function save_office_fixed_assets( $authorization, $office_code ) {
		$client = $this->plugin->get_client( \get_post( $authorization ) );

		$organisation = $client->get_organisation();

		$office = $organisation->office( $office_code );

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

		$fixed_assets_service = $client->get_service( 'fixed-assets' );

		$response = $fixed_assets_service->get_assets( $office );

		$fixed_assets = $response->to_fixed_assets();

		foreach ( $$fixed_assets as $fixed_asset ) {
			$this->log( \wp_json_encode( $fixed_asset ) );

			$fixed_asset_id = $orm->update_or_create(
				$fixed_asset,
				[
					'office_id'    => $office_id,
					'twinfield_id' => $fixed_asset->id,
					'code'         => $fixed_asset->code,
				],
				[
					'status'      => $fixed_asset->name,
					'description' => $fixed_asset->description,
				]
			);
		}
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
