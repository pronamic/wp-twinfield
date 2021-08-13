<?php
/**
 * Select company result
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Select company result
 *
 * This class contains constants for different Twinfield select company results.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SelectCompanyResult {
	/**
	 * Ok Log-on successful.
	 *
	 * @var string
	 */
	const OK = 'Ok';

	/**
	 * Log-on is blocked, because of system maintenance.
	 *
	 * @var string
	 */
	const INVALID = 'Invalid';
}
