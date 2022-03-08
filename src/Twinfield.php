<?php
/**
 * Twinfield
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use Pronamic\WordPress\Twinfield\Organisations\Organisation;

/**
 * Twinfield
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Twinfield {
	/**
	 * Organisations.
	 *
	 * @var array
	 */
	private $organisations;

	/**
	 * Constructs and initializes a Twinfield object.
	 */
	public function __construct() {
		$this->organisations = $organisation;
	}

	public function new_organisation( $code ) {
		$organisation = new Organisation( $code );

		$this->organisations[ $code ] = $organisation;

		return $organisation;
	}

	/**
	 * Get finder types.
	 * 
	 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Miscellaneous/Finder
	 * @return array<string, string>
	 */
	public function get_finder_types() {
		return [
			'ART'  => \__( 'Items', 'pronamic-twinfield' ),
			'ASM'  => \__( 'Asset methods', 'pronamic-twinfield' ),
			'BDS'  => \__( 'Budgets	code', 'pronamic-twinfield' ),
			'BNK'  => \__( 'Cashbooks and banks', 'pronamic-twinfield' ),
			'BSN'  => \__( 'Billing schedule', 'pronamic-twinfield' ),
			'CDA'  => \__( 'Credit management action codes', 'pronamic-twinfield' ),
			'CER'  => \__( 'Certificates', 'pronamic-twinfield' ),
			'CQT'  => \__( 'List of available (not paid) cheques', 'pronamic-twinfield' ),
			'CTR'  => \__( 'Countries', 'pronamic-twinfield' ),
			'CUR'  => \__( 'Currencies', 'pronamic-twinfield' ),
			'DIM'  => \__( 'Dimensions (financials)', 'pronamic-twinfield' ),
			'DIM'  => \__( 'Dimensions (modifiedsince option)', 'pronamic-twinfield' ),
			'DIM'  => \__( 'Dimensions (projects)', 'pronamic-twinfield' ),
			'DMT'  => \__( 'Dimension types', 'pronamic-twinfield' ),
			'DVT'  => \__( 'Vat types', 'pronamic-twinfield' ),
			'FLT'  => \__( 'Filter mappings', 'pronamic-twinfield' ),
			'FMT'  => \__( 'Payment files', 'pronamic-twinfield' ),
			'GRP'  => \__( 'Dimension groups', 'pronamic-twinfield' ),
			'GWY'  => \__( 'Gateways', 'pronamic-twinfield' ),
			'HIE'  => \__( 'Hierarchies', 'pronamic-twinfield' ),
			'HND'  => \__( 'Hierarchy nodes', 'pronamic-twinfield' ),
			'INV'  => \__( 'Invoice types', 'pronamic-twinfield' ),
			'IVT'  => \__( 'List of available (not paid) invoices', 'pronamic-twinfield' ),
			'MAT'  => \__( 'Matching types', 'pronamic-twinfield' ),
			'OFF'  => \__( 'Offices', 'pronamic-twinfield' ),
			'OFG'  => \__( 'Office groups', 'pronamic-twinfield' ),
			'OIC'  => \__( 'Available offices for InterCompany transactions', 'pronamic-twinfield' ),
			'PAY'  => \__( 'Payment types', 'pronamic-twinfield' ),
			'PIS'  => \__( 'Paying-in slips', 'pronamic-twinfield' ),
			'PRD'  => \__( 'Periods', 'pronamic-twinfield' ),
			'REP'  => \__( 'Reports', 'pronamic-twinfield' ),
			'REW'  => \__( 'Word templates (invoices, reminder letters)', 'pronamic-twinfield' ),
			'RMD'  => \__( 'Reminder scenarios', 'pronamic-twinfield' ),
			'ROL'  => \__( 'User roles', 'pronamic-twinfield' ),
			'SAR'  => \__( 'Sub items', 'pronamic-twinfield' ),
			'SPM'  => \__( 'Distribution by periods (Budgets/Extended Trial Balance allocation)', 'pronamic-twinfield' ),
			'TXG'  => \__( 'Tax groups', 'pronamic-twinfield' ),
			'TEQ'  => \__( 'Time & quantities transaction types', 'pronamic-twinfield' ),
			'TRS'  => \__( 'Transaction types (daybooks)', 'pronamic-twinfield' ),
			'TRT'  => \__( 'Time project rates', 'pronamic-twinfield' ),
			'USR'  => \__( 'Users', 'pronamic-twinfield' ),
			'VAT'  => \__( 'VAT codes', 'pronamic-twinfield' ),
			'VATN' => \__( 'VAT numbers of relations', 'pronamic-twinfield' ),
			'VTB'  => \__( 'VAT Groups', 'pronamic-twinfield' ),
			'VGM'  => \__( 'VAT Groups countries', 'pronamic-twinfield' ),
			'XLT'  => \__( 'Translations', 'pronamic-twinfield' ),
		];
	}

	public function get_browse_codes() {
		return [
			'000'   => \__( 'General ledger transactions', 'pronamic-twinfield' ),
			'010'   => \__( 'Transactions still to be matched', 'pronamic-twinfield' ),
			'020'   => \__( 'Transaction list', 'pronamic-twinfield' ),
			'100'   => \__( 'Customer transactions', 'pronamic-twinfield' ),
			'200'   => \__( 'Supplier transactions', 'pronamic-twinfield' ),
			'300'   => \__( 'Project transactions', 'pronamic-twinfield' ),
			'301'   => \__( 'Asset transactions', 'pronamic-twinfield' ),
			'400'   => \__( 'Cash transactions', 'pronamic-twinfield' ),
			'410'   => \__( 'Bank transactions', 'pronamic-twinfield' ),
			'900'   => \__( 'Cost centers', 'pronamic-twinfield' ),
			'030_1' => \__( 'General Ledger (details)', 'pronamic-twinfield' ),
			'030_2' => \__( 'General Ledger (details) (v2)', 'pronamic-twinfield' ),
			'030_3' => \__( 'General Ledger (details) (v3)', 'pronamic-twinfield' ),
			'031'   => \__( 'General Ledger (intercompany)', 'pronamic-twinfield' ),
			'031_2' => \__( 'General Ledger (intercompany)(v2)', 'pronamic-twinfield' ),
			'040_1' => \__( 'Annual Report (totals)', 'pronamic-twinfield' ),
			'050_1' => \__( 'Annual Report (YTD)', 'pronamic-twinfield' ),
			'060'   => \__( 'Annual Report (totals multicurrency)', 'pronamic-twinfield' ),
			'130_1' => \__( 'Customers', 'pronamic-twinfield' ),
			'130_2' => \__( 'Customers (v2)', 'pronamic-twinfield' ),
			'130_3' => \__( 'Customers (v3)', 'pronamic-twinfield' ),
			'164'   => \__( 'Credit Management', 'pronamic-twinfield' ),
			'230_1' => \__( 'Suppliers', 'pronamic-twinfield' ),
			'230_2' => \__( 'Suppliers (v2)', 'pronamic-twinfield' ),
			'302_1' => \__( 'Fixed Assets', 'pronamic-twinfield' ),
			'610_1' => \__( 'Time & Expenses (Totals)', 'pronamic-twinfield' ),
			'620'   => \__( 'Time & Expenses (Multicurrency)', 'pronamic-twinfield' ),
			'650_1' => \__( 'Time & Expenses (Details)', 'pronamic-twinfield' ),
			'651_1' => \__( 'Time & Expenses (Totals per week)', 'pronamic-twinfield' ),
			'652_1' => \__( 'Time & Expenses (Totals per period)', 'pronamic-twinfield' ),
			'660_1' => \__( 'Time & Expenses (Billing details)', 'pronamic-twinfield' ),
			'661_1' => \__( 'Time & Expenses (Billing per week)', 'pronamic-twinfield' ),
			'662_1' => \__( 'Time & Expenses (Billing per period)', 'pronamic-twinfield' ),
			'670'   => \__( 'Transaction summary', 'pronamic-twinfield' ),
			'680'   => \__( 'Bank link details', 'pronamic-twinfield' ),
			'690'   => \__( 'Vat Return status', 'pronamic-twinfield' ),
			'700'   => \__( 'Hierarchy access', 'pronamic-twinfield' ),
		];
	}
}
