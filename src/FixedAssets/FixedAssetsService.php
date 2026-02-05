<?php
/**
 * Fixed assets service
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

use Pronamic\WordPress\Twinfield\Client;

/**
 * Fixed assets service class
 */
final class FixedAssetsService {
	/**
	 * Twinfield client.
	 *
	 * @var Client
	 */
	private Client $client;

	/**
	 * Construct fixed assets service.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		$this->client = $client;
	}

	/**
	 * Create request client for assets.
	 *
	 * @param string $organisation_id Organisation ID.
	 * @param string $company_id      Company ID.
	 * @return GetFixedAssetsRequestClient
	 */
	public function assets( string $organisation_id, string $company_id ): GetFixedAssetsRequestClient {
		return new GetFixedAssetsRequestClient( $this, $organisation_id, $company_id );
	}

	/**
	 * Get assets.
	 *
	 * @internal Used by GetFixedAssetsRequestClient.
	 * @link https://api.accounting.twinfield.com/Api/swagger/ui/index#/
	 * @param GetFixedAssetsRequest $request Request.
	 * @return GetFixedAssetsResponse
	 */
	public function get_assets( GetFixedAssetsRequest $request ): GetFixedAssetsResponse {
		$base_url = '/Api';

		$path = '/organisations/{organisationId}/companies/{companyId}/fixedassets/assets';

		$path = \strtr(
			$path,
			[
				'{organisationId}' => $request->organisation_id,
				'{companyId}'      => $request->company_id,
			]
		);

		$path .= $request->build();

		$authentication = $this->client->authenticate();

		$cluster_url = $authentication->get_validation()->cluster_url;

		$url = $cluster_url . $base_url . $path;

		$response = \wp_remote_get(
			$url,
			[
				'headers' => [
					'Accept'        => 'application/json',
					'Authorization' => 'Bearer ' . $authentication->get_tokens()->get_access_token(),
				],
			]
		);

		if ( \is_wp_error( $response ) ) {
			throw new \Exception( $response->get_error_message() );
		}

		$status_code = \wp_remote_retrieve_response_code( $response );

		if ( 200 !== $status_code ) {
			$body = \wp_remote_retrieve_body( $response );
			$data = \json_decode( $body );

			$message = 'Twinfield API request failed';

			if ( \is_object( $data ) && isset( $data->message ) ) {
				$message .= ': ' . $data->message;
			}

			$message .= ' (HTTP ' . $status_code . ')';

			throw new \Exception( $message );
		}

		$body = \wp_remote_retrieve_body( $response );

		return GetFixedAssetsResponse::from_json( $body );
	}
}
