<?php
/**
 * Deleted transactions service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\Session;

/**
 * Deleted transactions service
 *
 * This class connects to the Twinfield deleted transactions webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DeletedTransactionsService extends AbstractService {
	/**
	 * The Twinfield declarations WSDL URL.
	 *
	 * @var string
	 */
	public const WSDL_FILE = '/webservices/DeletedTransactionsService.svc?wsdl';

	/**
	 * Constructs and initializes a declarations service object.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		parent::__construct( self::WSDL_FILE, $client );

		$this->soap_header_authenication_name = 'Authentication';
	}

	/**
	 * Get deleted transactions.
	 *
	 * @link https://c3.twinfield.com/webservices/documentation/#/ApiReference/Transactions/DeletedTransactions
	 * @param DeletedTransactionsQuery $query Deleted transactions query.
	 * @return mixed
	 */
	public function get_deleted_transactions( DeletedTransactionsQuery $query ) {
		$soap_client = $this->get_soap_client();

		$result = $soap_client->__soapCall(
			'Query',
			[ $query->get_soap_var() ]
		);

		return DeletedTransactions::from_twinfield_object( $result );
	}
}
