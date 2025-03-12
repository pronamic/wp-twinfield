<?php
/**
 * Hierarchy service
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;

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
	 * Get hierarchy by code.
	 *
	 * @param string $hierarchy_code Hierarchy code.
	 * @return HierarchyLoadResponse
	 */
	public function get_hierarchy( $hierarchy_code ) {
		$soap_client = $this->get_soap_client();

		$result = $soap_client->Load(
			(object) [
				'hierarchyCode' => $hierarchy_code,
			]
		);

		return HierarchyLoadResponse::from_object( $result );
	}
}
