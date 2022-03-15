<?php
/**
 * Get budget by profit and loss.
 *
 * @since 1.0.0
 * @package Pronamic/WP/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Budget;

use Pronamic\WordPress\Twinfield\Query;
use SoapVar;

/**
 * Get budget by profit and loss.
 *
 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Masters/Budget
 * @since 1.0.0
 * @package Pronamic/WP/Twinfield
 * @author Remco Tolsma <info@remcotolsma.nl>
 */
class BudgetByProfitAndLossQuery {
	/**
	 * Code of the budget to be retrieved.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * Year to be retrieved.
	 *
	 * @var int
	 */
	private $year;

	/**
	 * Start period to be retrieved.
	 *
	 * @var int
	 */
	private $period_from;

	/**
	 * End period to be retrieved.
	 *
	 * @var int
	 */
	private $period_to;

	/**
	 * Include provisional transactions.
	 *
	 * @var bool
	 */
	private $include_provisional;

	/**
	 * Include final transactions.
	 *
	 * @var bool
	 */
	private $include_final;

	/**
	 * Construct get budget by profit and loss query.
	 * 
	 * @param string $code                Budget code.
	 * @param int    $year                Year.
	 * @param int    $period_from         Period from.
	 * @param int    $period_to           Period to.
	 * @param bool   $include_provisional Include provisional.
	 * @param bool   $include_final       Include final.
	 */
	public function __construct( $code, $year, $period_from, $period_to, $include_provisional, $include_final ) {
		$this->code                = $code;
		$this->year                = $year;
		$this->period_from         = $period_from;
		$this->period_to           = $period_to;
		$this->include_provisional = $include_provisional;
		$this->include_final       = $include_final;
	}

	/**
	 * Get SOAP data.
	 * 
	 * @link https://www.php.net/manual/en/class.soapvar.php
	 * @return array
	 */
	private function get_soap_data() {
		return [
			'Code'               => $this->code,
			'Year'               => $this->year,
			'PeriodFrom'         => $this->period_from,
			'PeriodTo'           => $this->period_to,
			'IncludeProvisional' => $this->include_provisional,
			'IncludeFinal'       => $this->include_final,
		];
	}

	/**
	 * Get SOAP variable.
	 * 
	 * @return SoapVar
	 */
	public function get_soap_var() {
		return new SoapVar(
			$this->get_soap_data(),
			\SOAP_ENC_OBJECT,
			'GetBudgetByProfitAndLoss',
			'http://schemas.datacontract.org/2004/07/Twinfield.WebServices.BudgetService'
		);
	}
}
