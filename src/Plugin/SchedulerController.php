<?php
/**
 * Scheduler controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Scheduler controller class
 */
class SchedulerController {
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
		\add_action( 'init', [ $this, 'schedule_actions' ] );

		\add_action( 'pronamic_twinfield_pull', [ $this, 'pull' ] );

		\add_action( 'pronamic_twinfield_pull_offices', [ $this, 'pull_offices' ], 10, 1 );
	}

	/**
	 * Schedule actions.
	 *
	 * @return void
	 */
	public function schedule_actions() {
		if ( false === \as_has_scheduled_action( 'pronamic_twinfield_pull' ) ) {
			\as_schedule_recurring_action( \time(), '0 * * * *', 'pronamic_twinfield_pull', [], 'pronamic-twinfield' );
		}
	}

	/**
	 * Pull.
	 *
	 * @return void
	 */
	public function synchronize() {
		$query = new WP_Query(
			[
				'post_type'      => 'pronamic_twf_auth',
				'posts_per_page' => -1,
			]
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				\as_enqueue_async_action(
					'pronamic_twinfield_pull_offices',
					[
						'authorization' => \get_the_ID(),
					],
					'pronamic-twinfield'
				);
			}
		}
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

		\rest_do_request( $request );
	}
}
