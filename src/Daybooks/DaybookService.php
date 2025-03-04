<?php
/**
 * Daybook service
 *
 * @package Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Daybooks;

use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\Finder\Search;
use Pronamic\WordPress\Twinfield\Finder\SearchResponse;

/**
 * Daybook service class
 */
class DaybookService {
	/**
	 * Client.
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * Construct daybook service.
	 *
	 * @param Client $client Client.
	 */
	public function __construct( Client $client ) {
		$this->client = $client;
	}

	/**
	 * Search daybooks
	 * 
	 * @param Office $office    Office.
	 * @param string $pattern   Pattern.
	 * @param int    $field     Field.
	 * @param int    $first_row First row.
	 * @param int    $max_rows  Max rows.
	 * @param array  $options   Options.
	 * @return SearchResponse
	 */
	public function search_daybooks(
		Office $office,
		string $pattern = '*', 
		int $field = 0, 
		int $first_row = 1, 
		int $max_rows = 100, 
		array $options = [] 
	) {
		$finder = $this->client->get_finder();

		$finder->set_office( $office );

		$search = new Search( 'TRS', $pattern, $field, $first_row, $max_rows, $options );

		$response = $finder->search( $search );

		return $response;
	}
}
