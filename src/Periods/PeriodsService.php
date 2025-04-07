<?php
/**
 * Periods service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Periods;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;
use SoapHeader;
use SoapVar;

/**
 * Periods service
 *
 * This class connects to the Twinfield periods Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 * @link       https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Miscellaneous/Period
 */
class PeriodsService extends AbstractService {
	/**
	 * The Twinfield declarations WSDL URL.
	 *
	 * @var string
	 */
	public const WSDL_FILE = '/webservices/PeriodService.svc?wsdl';

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
	 * Get years.
	 *
	 * @link   https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Miscellaneous/Period#Queries
	 * @param Office $office Office.
	 * @return array
	 */
	public function get_years( $office ) {
		$soap_client = $this->get_soap_client( $office );

		$authentication = $this->client->authenticate();

		$result = $soap_client->__soapCall(
			'Query',
			[
				new SoapVar(
					[],
					\SOAP_ENC_OBJECT,
					'GetYears',
					'http://schemas.datacontract.org/2004/07/Twinfield.WebServices.PeriodService'
				),
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

		return ObjectAccess::from_object( $result )->get_object( 'Years' )->get_array( 'int' );
	}

	/**
	 * Get periods.
	 *
	 * @link   https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Miscellaneous/Period#Queries
	 * @param Office $office Office.
	 * @param int    $year   Year.
	 * @return array
	 */
	public function get_periods( $office, $year ) {
		$soap_client = $this->get_soap_client( $office );

		$authentication = $this->client->authenticate();

		$result = $soap_client->__soapCall(
			'Query',
			[
				new SoapVar(
					[
						'Year' => $year,
					],
					\SOAP_ENC_OBJECT,
					'GetPeriods',
					'http://schemas.datacontract.org/2004/07/Twinfield.WebServices.PeriodService'
				),
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

		$period_array = ObjectAccess::from_object( $result )->get_object( 'Periods' )->get_array( 'Period' );

		$periods = \array_map(
			fn( $item ) => Period::from_twinfield_object( $year, $item ),
			$period_array
		);

		return $periods;
	}
}
