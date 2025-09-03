<?php
/**
 * Fixed assets service
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;

/**
 * Fixed assets service class
 */
class FixedAssetsService {
	/**
	 * Get assets.
	 *
	 * @link https://api.accounting.twinfield.com/Api/swagger/ui/index#/
	 * @param string $organisation_id Organisation ID.
	 * @param string $company_id      Company ID.
	 * @return array
	 */
	public function get_assets( $organisation_id, $company_id ) {
	}
}
