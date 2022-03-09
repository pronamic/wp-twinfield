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
use Pronamic\WordPress\Twinfield\Organisation\Organisation;
use Pronamic\WordPress\Twinfield\Traits\CodeTrait;
use Pronamic\WordPress\Twinfield\Traits\NameTrait;

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
	use CodeTrait;

	use NameTrait;

	/**
	 * Organisation.
	 *
	 * @var Organisation|null
	 */
	public $organisation;

	/**
	 * Get organisation.
	 *
	 * @return Organisation|null
	 */
	public function get_organisation() {
		return $this->organisation;
	}
}
