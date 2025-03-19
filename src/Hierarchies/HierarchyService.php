<?php
/**
 * Hierarchy service
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\Finder\Search;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Hierarchy service class
 */
class HierarchyService extends AbstractService {
	/**
	 * The Twinfield finder WSDL URL.
	 *
	 * @var string
	 */
	public const WSDL_FILE = '/webservices/hierarchies.asmx?wsdl';

	/**
	 * Constructs and initializes an finder object.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		parent::__construct( self::WSDL_FILE, $client );
	}

	/**
	 * Search hierarchy (reporting structure) codes.
	 *
	 * @param string $pattern   Pattern.
	 * @param int    $field     Field.
	 * @param int    $first_row First row.
	 * @param int    $max_rows  Max rows.
	 * @param array  $options   Options.
	 * @return HierarchyCodeSearchResponse
	 */
	public function search_hierarchies(
		Office $office,
		string $pattern = '*', 
		int $field = 0, 
		int $first_row = 1, 
		int $max_rows = 100, 
		array $options = [] 
	) {
		$finder = $this->client->get_finder();

		$finder->set_office( $office );

		$search = new Search( 'HIE', $pattern, $field, $first_row, $max_rows, $options );

		$response = $finder->search( $search );

		return new HierarchyCodeSearchResponse( $response );
	}

	/**
	 * Get hierarchy by code.
	 *
	 * @param string $hierarchy_code Hierarchy code.
	 * @return HierarchyLoadResponse
	 */
	public function get_hierarchy( Office $office, $hierarchy_code ) {
		$soap_client = $this->get_soap_client( $office );

		$result = $soap_client->Load(
			(object) [
				'hierarchyCode' => $hierarchy_code,
			]
		);

		return HierarchyLoadResponse::from_object( $result );
	}
}
