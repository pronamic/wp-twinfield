<?php
/**
 * List entities.
 *
 * @since      1.0.0
 * @see        https://c3.twinfield.com/webservices/documentation/#/GettingStarted/WebServicesOverview#List-entities
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Session
 *
 * This class represents an Twinfield session.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ListEntities {
	/**
	 * Offices.
	 *
	 * List of companies. Use the Finder web service instead.
	 *
	 * @see https://c3.twinfield.com/webservices/documentation/#/GettingStarted/WebServicesOverview#List-entities
	 * @var string
	 */
	public const OFFICES = 'offices';

	/**
	 * Dimensions.
	 *
	 * List of dimensions. Use the Finder web service instead.
	 *
	 * @see https://c3.twinfield.com/webservices/documentation/#/GettingStarted/WebServicesOverview#List-entities
	 * @var string
	 */
	public const DIMENSIONS = 'dimensions';

	/**
	 * Browse fields.
	 *
	 * List of available browse fields.
	 *
	 * @see https://c3.twinfield.com/webservices/documentation/#/GettingStarted/WebServicesOverview#List-entities
	 * @var string
	 */
	public const BROWSE_FIELDS = 'browsefields';

	/**
	 * Budgets.
	 *
	 * List of budgets. Use the Budgets web service instead.
	 *
	 * @see https://c3.twinfield.com/webservices/documentation/#/GettingStarted/WebServicesOverview#List-entities
	 * @var string
	 */
	public const BUDGETS = 'budgets';
}
