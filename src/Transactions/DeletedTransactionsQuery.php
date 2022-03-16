<?php
/**
 * Deleted transactions service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use DateTimeInterface;
use SoapVar;

/**
 * Deleted transactions service
 *
 * This class connects to the Twinfield deleted transactions webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DeletedTransactionsQuery {
	/**
	 * The Company code for which the deleted transactions should be read. Mandatory.
	 * 
	 * @var string
	 */
	private $company_code;

	/**
	 * Deleted daybook (transaction type). Optional.
	 * 
	 * @var string|null
	 */
	private $daybook;

	/**
	 * The Date from which deleted transactions should be read. Optional.
	 * 
	 * @var DateTimeInterface|null
	 */
	private $date_from;

	/**
	 * The Date to which deleted transactions should be read. Optional.
	 * 
	 * @var DateTimeInterface|null
	 */
	private $date_to;

	/**
	 * Construct deleted transactions query.
	 * 
	 * @param string                 $company_code The Company code for which the deleted transactions should be read. Mandatory.
	 * @param string|null            $daybook      Deleted daybook (transaction type). Optional.
	 * @param DateTimeInterface|null $date_from    The Date from which deleted transactions should be read. Optional.
	 * @param DateTimeInterface|null $date_to      The Date to which deleted transactions should be read. Optional.
	 */
	public function __construct( $company_code, $daybook = null, DateTimeInterface $date_from = null, DateTimeInterface $date_to = null ) {
		$this->company_code = $company_code;
		$this->daybook      = $daybook;
		$this->date_from    = $date_from;
		$this->date_to      = $date_to;
	}

	/**
	 * Get SOAP data.
	 * 
	 * @link https://www.php.net/manual/en/class.soapvar.php
	 * @return array
	 */
	private function get_soap_data() {
		return [
			'CompanyCode' => $this->company_code,
			'Daybook'     => $this->daybook,
			'DateFrom'    => null === $this->date_from ? null : $this->date_from->format( 'Y-m-d' ),
			'DateTo'      => null === $this->date_to ? null : $this->date_to->format( 'Y-m-d' ),
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
			'GetDeletedTransactions',
			'http://schemas.datacontract.org/2004/07/Twinfield.WebServices.DeletedTransactionsService'
		);
	}
}
