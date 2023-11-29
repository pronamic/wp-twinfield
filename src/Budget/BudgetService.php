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
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;
use SoapHeader;
use SoapVar;

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

		$this->soap_header_authenication_name = 'Authentication';
	}

	/**
	 * Get budget by profit and loss query.
	 * 
	 * @param Office                     $office Office.
	 * @param BudgetByProfitAndLossQuery $query  Query.
	 * @return mixed
	 */
	public function get_budget_by_profit_and_loss_query( Office $office, BudgetByProfitAndLossQuery $query ) {
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

		$totals = \array_map(
			function ( $value ) {
				return BudgetTotalResult::from_twinfield_object( $value );
			},
			ObjectAccess::from_object( $result )->get_object( 'BudgetTotals' )->get_array( 'GetBudgetTotalResult' )
		);

		return $totals;
	}
}
