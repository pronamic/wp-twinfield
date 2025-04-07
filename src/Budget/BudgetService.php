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
	 * Search budget codes.
	 * 
	 * @param Office $office    Office.
	 * @param string $pattern   Pattern.
	 * @param int    $field     Field.
	 * @param int    $first_row First row.
	 * @param int    $max_rows  Max rows.
	 * @param array  $options   Options.
	 * @return DaybookSearchResponse
	 */
	public function search_budget_codes(
		Office $office,
		string $pattern = '*', 
		int $field = 0, 
		int $first_row = 1, 
		int $max_rows = 100, 
		array $options = [] 
	) {
		$finder = $this->client->get_finder();

		$finder->set_office( $office );

		$search = new Search( 'BDS', $pattern, $field, $first_row, $max_rows, $options );

		$response = $finder->search( $search );

		return new BudgetCodeSearchResponse( $office, $response );
	}

	/**
	 * Get budget by profit and loss query.
	 * 
	 * @param Office                     $office Office.
	 * @param BudgetByProfitAndLossQuery $query  Query.
	 * @return BudgetTotalResult[]
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

		$budget_totals = ObjectAccess::from_object( $result )->get_object( 'BudgetTotals' );

		if ( ! $budget_totals->has_property( 'GetBudgetTotalResult' ) ) {
			return [];
		}

		$totals = \array_map(
			fn( $value ) => BudgetTotalResult::from_twinfield_object( $value ),
			$budget_totals->get_array( 'GetBudgetTotalResult' )
		);

		return $totals;
	}
}
