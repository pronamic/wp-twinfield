<?php
/**
 * Bank statements by creation date query
 *
 * @package Pronamic\WordPress\Twinfield
 */

namespace Pronamic\WordPress\Twinfield\BankStatements;

use DateTimeInterface;
use SoapVar;

/**
 * Bank statements by creation date query class
 */
class BankStatementsByCreationDateQuery {
	/**
	 * All statements with a statement date equal to or higher than this value will be included.
	 * 
	 * @var DateTimeInterface
	 */
	private $date_from;

	/**
	 * All statements with a statement date equal to or lower than this value will be included.
	 * 
	 * @var DateTimeInterface
	 */
	private $date_to;

	/**
	 * If value is true, statements that have been posted will be included.
	 * 
	 * @var bool
	 */
	private $include_posted_statements;

	/**
	 * Construct get budget by profit and loss query.
	 * 
	 * @param DateTimeInterface $date_from                 All statements with a statement date equal to or higher than this value will be included.
	 * @param DateTimeInterface $date_to                   All statements with a statement date equal to or lower than this value will be included.
	 * @param bool              $include_posted_statements If value is true, statements that have been posted will be included.
	 */
	public function __construct( DateTimeInterface $date_from, DateTimeInterface $date_to, $include_posted_statements ) {
		$this->date_from                 = $date_from;
		$this->date_to                   = $date_to;
		$this->include_posted_statements = $include_posted_statements;
	}

	/**
	 * Get SOAP data.
	 * 
	 * @link https://www.php.net/manual/en/class.soapvar.php
	 * @return array
	 */
	private function get_soap_data() {
		return [
			'CreationDateFrom'        => $this->date_from->format( \DATE_ATOM ),
			'CreationDateTo'          => $this->date_to->format( \DATE_ATOM ),
			'IncludePostedStatements' => $this->include_posted_statements,
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
			'GetBankStatementsByCreationDate',
			'http://schemas.datacontract.org/2004/07/Twinfield.WebServices.BankStatementService'
		);
	}
}
