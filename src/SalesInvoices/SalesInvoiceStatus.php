<?php
/**
 * Sales invoice status
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\SalesInvoices;

/**
 * Sales invoice status
 *
 * This class contains constants for different Twinfield sales invoice statuses.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SalesInvoiceStatus {
	/**
	 * Default.
	 *
	 * @var string
	 */
	public const STATUS_DEFAULT = 'default';

	/**
	 * Concept.
	 *
	 * @var string
	 */
	public const STATUS_CONCEPT = 'concept';

	/**
	 * Final.
	 *
	 * @var string
	 */
	public const STATUS_FINAL = 'final';
}
