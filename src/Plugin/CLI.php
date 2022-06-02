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
			'twinfield office list',
			function( $args, $assoc_args ) {
				$authorization_post_id = \array_key_exists( 'authorization', $assoc_args ) ? $assoc_args['authorization'] : \get_option( 'pronamic_twinfield_authorization_post_id' );

				$route = '/pronamic-twinfield/v1/authorizations/' . $authorization_post_id . '/offices';

				$request = new WP_REST_Request( 'GET', $route );

				$response = rest_do_request( $request );

				/**
				 * REST API endpoints permissions.
				 * 
				 * @link https://make.wordpress.org/cli/handbook/references/config/#global-parameters
				 * @link https://github.com/woocommerce/woocommerce/blob/6.5.1/plugins/woocommerce/includes/cli/class-wc-cli-rest-command.php#L349-L358
				 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#permissions-callback
				 */
				if ( 401 === $response->get_status() ) {
					\WP_CLI::error( 'Unauthorized, make sure to include the --user flag with an account that has permissions for this action.' );
				}

				$data = (object) $response->get_data();

				$offices = $data->data;

				$items = \array_map(
					function( $office ) {
						return [
							'code'      => $office->get_code(),
							'name'      => $office->get_name(),
							'shortname' => $office->get_shortname(),
						];
					},
					$offices
				);

				$formatter = new \WP_CLI\Formatter(
					$assoc_args,
					[
						'code',
						'name',
						'shortname',
					] 
				);

				$formatter->display_items( $items );
			} 
		);

		\WP_CLI::add_command(
			'twinfield bank-statement query',
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
