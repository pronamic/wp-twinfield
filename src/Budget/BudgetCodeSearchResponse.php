<?php
/**
 * Budget code search response
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Budget;

use Pronamic\WordPress\Twinfield\Finder\SearchResponse;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Budget code search response class
 */
class BudgetCodeSearchResponse {
	/**
	 * Office.
	 * 
	 * @var Office
	 */
	public Office $office;

	/**
	 * Search response.
	 * 
	 * @var SearchResponse
	 */
	public SearchResponse $response;

	/**
	 * Construct budget code search response.
	 * 
	 * @param Office         $office   Office.
	 * @param SearchResponse $response Response.
	 */
	public function __construct( Office $office, SearchResponse $response ) {
		$this->office   = $office;
		$this->response = $response;
	}

	/**
	 * To budget codes.
	 * 
	 * @return BudgetCode[]
	 */
	public function to_budget_codes() {
		$office = $this->office;

		$data = $this->response->get_data();

		$budget_codes = \array_map(
			function ( $item ) use ( $office ) {
				$budget_code = new BudgetCode( $office, $item[0] );

				$budget_code->name = $item[1];

				return $budget_code;
			},
			$data->get_items()
		);

		return $budget_codes;
	}
}
