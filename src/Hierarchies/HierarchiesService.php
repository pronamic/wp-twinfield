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
	const WSDL_FILE = '/webservices/hierarchies.asmx?wsdl';

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
	 * @return LoadResponse
	 */
	public function get_hierarchy( $hierarchy_code ) {
		$result = $this->soap_client->Load( (object) array(
			'hierarchyCode' => $hierarchy_code,
		) );

		return LoadResponse::from_object( $result );
	}
}
