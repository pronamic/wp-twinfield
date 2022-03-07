<?php
/**
 * Services
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Services;

/**
 * Services
 *
 * This class contains constants for different Twinfield services.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 * @see        https://accounting.twinfield.com/webservices/documentation/#/GettingStarted/WebServicesOverview
 */
class Services {
	/**
	 * Session.
	 *
	 * @var string
	 */
	public const SESSION = '/webservices/session.asmx?wsdl';

	/**
	 * Bank books.
	 *
	 * @var string
	 */
	public const BANK_BOOKS = '/webservices/bankbookservice.svc?wsdl';

	/**
	 * Bank statements.
	 *
	 * @var string
	 */
	public const BANK_STATEMENTS = '/webservices/bankstatementservice.svc?wsdl';

	/**
	 * Budgets.
	 *
	 * @var string
	 */
	public const BUDGETS = '/webservices/budgetservice.svc?wsdl';

	/**
	 * Cash books.
	 *
	 * @var string
	 */
	public const CASH_BOOKS = '/webservices/cashbookservice.svc?wsdl';

	/**
	 * Declarations.
	 *
	 * @var string
	 */
	public const DECLARATIONS = '/webservices/declarations.asmx?wsdl';

	/**
	 * Documents.
	 *
	 * @var string
	 */
	public const DOCUMENTS = '/webservices/documentservice.svc?wsdl';

	/**
	 * Finder.
	 *
	 * @var string
	 */
	public const FINDER = '/webservices/finder.asmx?wsdl';

	/**
	 * Hierarchies.
	 *
	 * @var string
	 */
	public const HIERARCHIES = '/webservices/hierarchies.asmx?wsdl';

	/**
	 * Matching.
	 *
	 * @var string
	 */
	public const MATCHING = '	/webservices/matching.asmx?wsdl';

	/**
	 * Pay and collect.
	 *
	 * @var string
	 */
	public const PAY_AND_COLLECT = '/webservices/payandcollect.asmx?wsdl';

	/**
	 * Pay type.
	 *
	 * @var string
	 */
	public const PAY_TYPE = '/webservices/paytype.asmx?wsdl';

	/**
	 * Periods
	 *
	 * @var string
	 */
	public const PERIODS = '/webservices/periodservice.svc?wsdl';

	/**
	 * ProcessXml.
	 *
	 * @var string
	 */
	public const PROCESS_XML = '/webservices/processxml.asmx?wsdl';

	/**
	 * XBRL.
	 *
	 * @var string
	 */
	public const XBRL = '/webservices/sbr.asmx?wsdl';

	/**
	 * Versions.
	 *
	 * @var string
	 */
	public const VERSIONS = '/webservices/versions.asmx?wsdl';

	/**
	 * Deleted transactions.
	 *
	 * @var string
	 */
	public const DELETED_TRANSACTIONS = '/webservices/deletedtransactionsservice.svc?wsdl';

	/**
	 * Blocked value.
	 *
	 * @var string
	 */
	public const BLOCKED_VALUE = '/webservices/transactionblockedvalueservice.svc?wsdl';
}
