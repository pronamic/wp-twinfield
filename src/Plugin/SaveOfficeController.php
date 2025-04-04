<?php
/**
 * Save office controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Offices\OfficeService;
use WP_REST_Request;
use WP_CLI;

/**
 * Save office controller class
 */
class SaveOfficeController {
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
		\add_action( 'pronamic_twinfield_pull_offices', [ $this, 'pull_offices' ], 10, 1 );
		\add_action( 'pronamic_twinfield_pull_office', [ $this, 'pull_office' ], 10, 2 );
	}

	/**
	 * Pull offices.
	 * 
	 * @param string|int $authorization Authorization.
	 * @return void
	 */
	public function pull_offices( $authorization ) {
		$request = new WP_REST_Request( 'GET', '/pronamic-twinfield/v1/authorizations/' . $authorization . '/offices' );

		$request->set_param( 'authorization', $authorization );
		$request->set_param( 'pull', true );

		$response = \rest_do_request( $request );

		$data = (object) $response->get_data();

		foreach ( $data->data as $item ) {
			$action_id = \as_enqueue_async_action(
				'pronamic_twinfield_pull_office',
				[
					'authorization' => $authorization,
					'office_code'   => $item->get_code(),
				],
				'pronamic-twinfield'
			);

			$this->log(
				\sprintf(
					'Saving office details is scheduled, authorization post ID: %s, office code: %s, action ID: %s.',
					$authorization,
					$item->get_code(),
					$action_id
				)
			);
		}
	}

	/**
	 * Pull office.
	 * 
	 * @param string|int $authorization Authorization.
	 * @param string     $office_code   Office code.
	 * @return void
	 */
	public function pull_office( $authorization, $office_code ) {
		$request = new WP_REST_Request( 'GET', '/pronamic-twinfield/v1/authorizations/' . $authorization . '/offices/' . $office_code );

		$request->set_param( 'authorization', $authorization );
		$request->set_param( 'pull', true );

		$response = \rest_do_request( $request );
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
