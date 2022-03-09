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
	 * @return array
	 */
	public function get_years( $office ) {
		$soap_client = $this->get_soap_client( $office );

		$query = new QueryGetYears();

		$result = $soap_client->Query( $query );

		if ( ! is_object( $result ) ) {
			throw new \Exception( 'Could not get years from Twinfield period service.' );
		}

		if ( ! property_exists( $result, 'Years' ) ) {
			throw new \Exception( 'Could not get years from Twinfield period service.' );
		}

		$years_object = $result->Years;

		if ( ! property_exists( $years_object, 'int' ) ) {
			throw new \Exception( 'Could not get years from Twinfield period service.' );
		}

		$int_array = $years_object->int;

		if ( ! is_array( $int_array ) ) {
			throw new \Exception( 'Could not get years from Twinfield period service.' );
		}

		return $int_array;
	}

	/**
	 * Get periods.
	 *
	 * @link   https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Miscellaneous/Period#Queries
	 * @return array
	 */
	public function get_periods( $office, $year ) {
		$soap_client = $this->get_soap_client( $office );

		$query       = new QueryGetPeriods();
		$query->Year = $year;

		$result = $soap_client->Query( $query );

		if ( ! is_object( $result ) ) {
			throw new \Exception( 'Could not get periods from Twinfield period service.' );
		}

		if ( ! property_exists( $result, 'Periods' ) ) {
			throw new \Exception( 'Could not get periods from Twinfield period service.' );
		}

		$periods_object = $result->Periods;

		if ( ! property_exists( $periods_object, 'Period' ) ) {
			throw new \Exception( 'Could not get periods from Twinfield period service.' );
		}

		$period_array = $periods_object->Period;

		if ( ! is_array( $period_array ) ) {
			throw new \Exception( 'Could not get periods from Twinfield period service.' );
		}

		$periods = \array_map(
			function( $item ) use ( $year ) {
				return Period::from_twinfield_object( $year, $item );
			},
			$period_array
		);

		return $periods;
	}
}
