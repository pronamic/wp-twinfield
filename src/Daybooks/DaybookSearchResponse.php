<?php
/**
 * Daybook search response
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Daybooks;

use JsonSerializable;
use Stringable;
use Pronamic\WordPress\Twinfield\Finder\SearchResponse;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Daybook search response class
 */
class DaybookSearchResponse {
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
	 * Construct daybook search response.
	 * 
	 * @param Office         $office   Office.
	 * @param SearchResponse $response Response.
	 */
	public function __construct( Office $office, SearchResponse $response ) {
		$this->office   = $office;
		$this->response = $response;
	}

	/**
	 * To daybooks.
	 * 
	 * @return Daybook[]
	 */
	public function to_daybooks() {

	}
}
