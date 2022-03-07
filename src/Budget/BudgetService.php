<?php
/**
 * Budget service
 *
 * @since 1.0.0
 * @package Pronamic/WP/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Budget;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;

/**
 * Budget service
 *
 * This class connects to the Twinfield budget Webservices.
 *
 * @since 1.0.0
 * @package Pronamic/WP/Twinfield
 * @author Remco Tolsma <info@remcotolsma.nl>
 */
class BudgetService extends AbstractService {
	/**
	 * The Twinfield budget WSDL URL.
	 *
	 * @var string
	 */
	public const WSDL_FILE = '/webservices/BudgetService.svc?wsdl';

	/**
	 * Constructs and initializes an finder object.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		parent::__construct( self::WSDL_FILE, $client );
	}
}
