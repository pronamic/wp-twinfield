<?php
/**
 * Deleted transactions service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use Pronamic\WordPress\Twinfield\Query;

/**
 * Deleted transactions service
 *
 * This class connects to the Twinfield deleted transactions webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class GetDeletedTransactions extends Query {
	public $CompanyCode;

	public $DateFrom;

	public $DateTo;

	public $Daybook;
}
