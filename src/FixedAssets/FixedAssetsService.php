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
	 * Get assets.
	 *
	 * @link https://api.accounting.twinfield.com/Api/swagger/ui/index#/
	 * @param string $organisation_id Organisation ID.
	 * @param string $company_id      Company ID.
	 * @return FixedAsset[]
	 */
	public function get_assets( $organisation_id, $company_id ) {
		$base_url = '/Api';

		$path = '/organisations/{organisationId}/companies/{companyId}/fixedassets/assets';

		$path = \strtr(
			$path,
			[
				'{organisationId}' => $organisation_id,
				'{companyId}'      => $company_id,
			]
		);

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

		$body = \wp_remote_retrieve_body( $response );
		$data = \json_decode( $body );

		if ( ! \is_object( $data ) || ! isset( $data->items ) ) {
			var_dump( $data );
			throw new \Exception( 'Invalid response from Twinfield API.' );
		}

		$assets = [];

		foreach ( $data->items as $asset_data ) {
			$assets[] = FixedAsset::from_object( $asset_data );
		}

		return $assets;
	}
}
