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
	/**
	 * Organisation.
	 *
	 * @var Organisation|null
	 */
	public ?Organisation $organisation;

	/**
	 * Construct user.
	 *
	 * @param string $code Code.
	 */
	public function __construct( $code ) {
		parent::__construct( $code );
	}
}
