<?php
/**
 * User
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Users;

use Pronamic\WordPress\Twinfield\CodeName;
use Pronamic\WordPress\Twinfield\Organisations\Organisation;
use Pronamic\WordPress\Twinfield\Traits\OrganisationTrait;

/**
 * User
 *
 * This class represents a Twinfield user
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class User extends CodeName {
	use OrganisationTrait;

	/**
	 * Construct user.
	 *
	 * @param Organisation $organisation Organisation.
	 * @param string       $code         Code.
	 */
	public function __construct( Organisation $organisation, $code ) {
		parent::__construct( $code );

		$this->organisation = $organisation;
	}
}
