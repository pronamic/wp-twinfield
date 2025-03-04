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
	 * @return SearchResponse
	 */
	public function search_daybooks() {
		$finder = $this->client->get_finder();

		$search = new Search(
			'TRS',
			'*',
			0,
			1,
			100,
			[
				'hidden' => '1',
			]
		);

		$response = $finder->search( $search );

		return $response;
	}
}
