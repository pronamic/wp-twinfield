<?php
/**
 * Save fixed asset controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\FixedAssets\FixedAssetsService;
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
		 * wp pronamic-twinfield save-office-fixed-assets --authorization=5337 --office_code=1000 --company_id=ffe2c8b3-e7f0-4793-a059-ffbfe3c154a9
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

				if ( ! \array_key_exists( 'company_id', $assoc_args ) ) {
					WP_CLI::error( 'Company ID argument missing.' );
				}

				$this->save_office_fixed_assets( $assoc_args['authorization'], $assoc_args['office_code'], $assoc_args['company_id'] );
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
		$client = $this->plugin->get_client( \get_post( $authorization ) );

		$finder = $client->get_finder();

		$offices = $finder->offices()
			->include_id()
			->limit( 500 )
			->get_offices();

		foreach ( $offices as $office ) {
			$office_code = $office->get_code();

			$action_id = \as_enqueue_async_action(
				'pronamic_twinfield_save_office_fixed_assets',
				[
					'authorization' => $authorization,
					'office_code'   => $office_code,
					'company_id'    => $office->id,
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
	 * @param string     $company_id    Company ID.
	 * @return void
	 */
	private function save_office_fixed_assets( $authorization, $office_code, $company_id ) {
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

		$fixed_assets_service = new FixedAssetsService( $client );

		$fixed_assets = $fixed_assets_service->assets( $organisation->get_uuid(), $company_id )
			->limit( 100 )
			->fields( '*' )
			->get();

		foreach ( $fixed_assets as $fixed_asset ) {
			$this->log( \wp_json_encode( $fixed_asset ) );

			$values = [
				'status'      => $fixed_asset->status,
				'description' => $fixed_asset->description,
				'json'        => \wp_json_encode( $fixed_asset ),
			];

			if ( null !== $fixed_asset->youngest_balances?->net_book_value ) {
				$values['net_book_value'] = $fixed_asset->youngest_balances?->net_book_value;
			}

			if ( null !== $fixed_asset->youngest_balances?->purchase_value ) {
				$values['purchase_value'] = $fixed_asset->youngest_balances?->purchase_value;
			}

			$fixed_asset_id = $orm->update_or_create(
				$fixed_asset,
				[
					'office_id'    => $office_id,
					'twinfield_id' => $fixed_asset->id,
					'code'         => $fixed_asset->code,
				],
				$values
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
