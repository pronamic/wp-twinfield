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

	public function get_budget_by_profit_and_loss_query( Office $office, $code, $year, $period_from, $period_to, $include_provisional, $include_final ) {
		$soap_client = $this->get_soap_client( $office );

		$get_budget_by_profit_and_loss_query = new GetBudgetByProfitAndLossQuery();

		$get_budget_by_profit_and_loss_query->Code               = '001';
		$get_budget_by_profit_and_loss_query->Year               = 2022;
		$get_budget_by_profit_and_loss_query->PeriodFrom         = 1;
		$get_budget_by_profit_and_loss_query->PeriodTo           = 13;
		$get_budget_by_profit_and_loss_query->IncludeProvisional = true;
		$get_budget_by_profit_and_loss_query->IncludeFinal       = true;

		$data = [
			'Code'               => '001',
			'Year'               => 2022,
			'PeriodFrom'         => 1,
			'PeriodTo'           => 13,
			'IncludeProvisional' => true,
			'IncludeFinal'       => true,
		];

		$test = new \SoapVar( $data, \SOAP_ENC_OBJECT, 'GetBudgetByProfitAndLoss', 'http://schemas.datacontract.org/2004/07/Twinfield.WebServices.BudgetService' );

		try {
			$result = $soap_client->Query( $test );
		} catch ( \Exception $e ) {
			var_dump( $e );
		}

		var_dump( $soap_client->__getLastRequest() );

		return $result;
	}
}
