<?php
/**
 * Hierarchy
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;

/**
 * Hierarchy
 *
 * This class connects to the Twinfield hierarchy Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class HierarchiesService extends AbstractService {
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
	 * @return LoadResponse
	 */
	public function get_hierarchy( $hierarchy_code ) {
		$soap_client = $this->get_soap_client();

		$result = $soap_client->Load(
			(object) [
				'hierarchyCode' => $hierarchy_code,
			]
		);

		return LoadResponse::from_object( $result );
	}
}
