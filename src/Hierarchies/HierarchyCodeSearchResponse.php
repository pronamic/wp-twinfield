<?php
/**
 * Hierarchy code search response
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use Pronamic\WordPress\Twinfield\Finder\SearchResponse;

/**
 * Hierarchy code search response class
 */
final class HierarchyCodeSearchResponse {
	/**
	 * Search response.
	 * 
	 * @var SearchResponse
	 */
	public SearchResponse $response;

	/**
	 * Construct hierarchy code search response.
	 * 
	 * @param SearchResponse $response Response.
	 */
	public function __construct( SearchResponse $response ) {
		$this->response = $response;
	}

	/**
	 * To hierarchy codes.
	 * 
	 * @return BudgetCode[]
	 */
	public function to_hierarchy_codes() {
		$data = $this->response->get_data();

		$hierarchy_codes = \array_map(
			function ( $item ) {
				$hierarchy_code = new HierarchyCode( $item[0] );

				$hierarchy_code->name = $item[1];

				return $hierarchy_code;
			},
			$data->get_items()
		);

		return $hierarchy_codes;
	}
}
