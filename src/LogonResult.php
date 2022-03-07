<?php
/**
 * Logon result
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Long result
 *
 * This class contains constants for different Twinfield logon result codes.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class LogonResult {
	/**
	 * Ok Log-on successful.
	 *
	 * @var string
	 */
	public const OK = 'Ok';

	/**
	 * Log-on is blocked, because of system maintenance.
	 *
	 * @var string
	 */
	public const BLOCKED = 'Blocked';

	/**
	 * Log-on is not trusted.
	 *
	 * @var string
	 */
	public const UNTRUSTED = 'Untrusted';

	/**
	 * Log-on is invalid.
	 *
	 * @var string
	 */
	public const INVALID = 'Invalid';

	/**
	 * User is deleted.
	 *
	 * @var string
	 */
	public const DELETED = 'Deleted';

	/**
	 * User is disabled.
	 *
	 * @var string
	 */
	public const DISABLED = 'Disabled';

	/**
	 * Organization is inactive.
	 *
	 * @var string
	 */
	public const ORGANISATION_INACTIVE = 'OrganisationInactive';
}
