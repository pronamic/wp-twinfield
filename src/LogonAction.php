<?php
/**
 * Logon action
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Logon action
 *
 * This class contains constants for different Twinfield logon actions.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class LogonAction {
	/**
	 * No action required.
	 *
	 * @var string
	 */
	public const NONE = 'None';

	/**
	 * The next required action is SMS authentication.
	 *
	 * @var string
	 */
	public const SMS_LOGON = 'SMSLogon';

	/**
	 * The next required action is change of password.
	 *
	 * @var string
	 */
	public const CHANGE_PASSWORD = 'ChangePassword';
}
