<?php
/**
 * CLI
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_REST_Request;

/**
 * CLI class
 */
class CLI {
	/**
	 * Construct CLI.
	 */
	public function __construct() {
		\WP_CLI::add_command(
			'twinfield offices list',
			function( $args, $assoc_args ) {
				$authorization_post_id = \array_key_exists( 'authorization', $assoc_args ) ? $assoc_args['authorization'] : \get_option( 'pronamic_twinfield_authorization_post_id' );

				$route = '/pronamic-twinfield/v1/authorizations/' . $authorization_post_id . '/offices';

				$request = new WP_REST_Request( 'GET', $route );

				$response = rest_do_request( $request );

				$data = (object) $response->get_data();

				$offices = $data->data;

				$items = \array_map(
					function( $office ) {
						return [
							'code' => $office->get_code(),
							'name' => $office->get_name(),
						];
					},
					$offices
				);

				$formatter = new \WP_CLI\Formatter(
					$assoc_args,
					[
						'code',
						'name',
					] 
				);

				$formatter->display_items( $items );
			} 
		);

		\WP_CLI::add_command(
			'twinfield bank-statements',
			function( $args, $assoc_args ) {
				$authorization_post_id = \array_key_exists( 'authorization', $assoc_args ) ? $assoc_args['authorization'] : \get_option( 'pronamic_twinfield_authorization_post_id' );

				$items = [];

				try {
					foreach ( $args as $office_code ) {
						$route = '/pronamic-twinfield/v1/authorizations/' . $authorization_post_id . '/offices/' . $office_code . '/bank-statements';

						$request = new WP_REST_Request( 'GET', $route );

						foreach ( $assoc_args as $key => $value ) {
							$request->set_param( $key, $value );
						}

						$response = rest_do_request( $request );

						$bank_statements = (object) $response->get_data();

						foreach ( $bank_statements as $bank_statement ) {
							$data = $bank_statement->jsonSerialize();

							$items[] = [
								'office_code'        => $office_code,
								'date'               => $bank_statement->get_date()->format( 'Y-m-d' ),
								'code'               => $data->code,
								'number'             => $data->number,
								'sub_id'             => $data->sub_id,
								'currency'           => $data->currency,
								'opening_balance'    => $data->opening_balance,
								'closing_balance'    => $data->closing_balance,
								'transaction_number' => $data->currency,
							];
						}
					}
				} catch ( \Exception $e ) {
					\WP_CLI::error( $e->getMessage() );
				}

				$formatter = new \WP_CLI\Formatter(
					$assoc_args,
					[
						'office_code',
						'date',
						'code',
						'number',
						'sub_id',
						'currency',
						'opening_balance',
						'closing_balance',
						'transaction_number',
					] 
				);

				$formatter->display_items( $items );
			} 
		);
	}
}
