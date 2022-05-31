<?php
/**
 * Bank statements service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\BankStatements;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;
use SoapVar;
use SoapHeader;

/**
 * Bank statements service class
 *
 * This class connects to the Twinfield declarations Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class BankStatementsService extends AbstractService {
	/**
	 * The Twinfield bank statements WSDL URL.
	 *
	 * @var string
	 */
	public const WSDL_FILE = '/webservices/BankStatementService.svc?wsdl';

	/**
	 * Constructs and initializes a declarations service object.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		parent::__construct( self::WSDL_FILE, $client );
	}

	/**
	 * Get bank statements.
	 *
	 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Request/BrowseBankStatements
	 * @param Office              $office The office for which the returns should be retrieved. Mandatory.
	 * @param BankStatementsQuery $query  The get bank statements query.
	 * @return BankStatements
	 * @throws \Exception When no summaries could be found for the specified office.
	 */
	public function get_bank_statements( Office $office, BankStatementsQuery $query ) {
		$soap_client = $this->get_soap_client( $office );

		$authentication = $this->client->authenticate();

		$result = $soap_client->__soapCall(
			'Query',
			[
				$query->get_soap_var(),
			],
			null,
			new SoapHeader(
				'http://www.twinfield.com/',
				'Authentication',
				[
					'AccessToken' => $authentication->get_tokens()->get_access_token(),
					'CompanyCode' => $office->get_code(),
				]
			)
		);

		return BankStatements::from_twinfield_object(
			$office,
			ObjectAccess::from_object( $result )->get_property( 'BankStatements' )
		);
	}
}
