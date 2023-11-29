<?php
/**
 * Finder
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Documents;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;

/**
 * Finder
 *
 * This class connects to the Twinfield finder Webservices to search for Twinfield masters.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DocumentService extends AbstractService {
	/**
	 * The Twinfield finder WSDL URL.
	 *
	 * @var string
	 */
	public const WSDL_FILE = '/webservices/DocumentService.svc?wsdl';

	/**
	 * Constructs and initializes an finder object.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		parent::__construct( self::WSDL_FILE, $client );
	}

	/**
	 * Query.
	 */
	public function query() {
	}
}
