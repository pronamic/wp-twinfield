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
	 * @see https://c3.twinfield.com/webservices/documentation/#/ApiReference/Transactions/DeletedTransactions
	 */
	public function get_deleted_transactions( $office_code, \DateTimeInterface $date_from = null, \DateTimeInterface $date_to = null ) {
		$soap_client = $this->get_soap_client();

		$query              = new GetDeletedTransactions();
		$query->CompanyCode = $office_code;

		if ( null !== $date_from ) {
			$query->DateFrom = $date_from->format( 'Y-m-d' );
		}

		if ( null !== $date_to ) {
			$query->DateTo = $date_to->format( 'Y-m-d' );
		}

		$result = $soap_client->Query( $query );

		if ( ! isset( $result->DeletedTransactions ) ) {
			return false;
		}

		if ( ! isset( $result->DeletedTransactions->DeletedTransaction ) ) {
			return false;
		}

		$transactions = false;

		$data = $result->DeletedTransactions->DeletedTransaction;

		if ( is_object( $data ) ) {
			$transactions = [ $data ];
		}

		if ( is_array( $data ) ) {
			$transactions = $data;
		}

		return $transactions;
	}
}
