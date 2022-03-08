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
}
