<?php
/**
 * Get fixed assets request client
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

/**
 * Get fixed assets request client class
 *
 * Extends GetFixedAssetsRequest with the ability to execute the request.
 * Follows HTTPlug philosophy: request is independent of client.
 */
final class GetFixedAssetsRequestClient extends GetFixedAssetsRequest {
	/**
	 * Fixed assets service.
	 *
	 * @var FixedAssetsService
	 */
	private FixedAssetsService $service;

	/**
	 * Construct get fixed assets request client.
	 *
	 * @param FixedAssetsService $service         Fixed assets service.
	 * @param string             $organisation_id Organisation ID.
	 * @param string             $company_id      Company ID.
	 */
	public function __construct( FixedAssetsService $service, string $organisation_id, string $company_id ) {
		parent::__construct( $organisation_id, $company_id );
		$this->service = $service;
	}

	/**
	 * Execute request and get response.
	 *
	 * @return GetFixedAssetsResponse
	 */
	public function get(): GetFixedAssetsResponse {
		return $this->service->get_assets( $this );
	}
}
