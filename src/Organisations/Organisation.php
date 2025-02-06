<?php
/**
 * Organisation
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Organisations;

use Pronamic\WordPress\Twinfield\CodeName;
use Pronamic\WordPress\Twinfield\UuidTrait;
use Pronamic\WordPress\Twinfield\Twinfield;
use Pronamic\WordPress\Twinfield\Users\User;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Traits\CodeTrait;
use Pronamic\WordPress\Twinfield\Traits\NameTrait;
use Pronamic\WordPress\Twinfield\Traits\ShortnameTrait;

/**
 * Organisation
 *
 * This class represents a Twinfield organisation
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Organisation extends CodeName {
	/**
	 * Twinfield.
	 * 
	 * @var Twinfield
	 */
	private $twinfield;

	use UuidTrait;

	/**
	 * Users.
	 *
	 * @var User[]
	 */
	private $users;

	/**
	 * Offices.
	 *
	 * @var Office[]
	 */
	private $offices;

	/**
	 * Construct Twinfield organisation.
	 *
	 * @param string $code Code.
	 */
	public function __construct( $code ) {
		$this->set_code( $code );

		$this->twinfield = new Twinfield();

		$this->users   = [];
		$this->offices = [];
	}

	/**
	 * Get Twinfield.
	 *
	 * @return Twinfield
	 */
	public function get_twinfield() {
		return $this->twinfield;
	}

	/**
	 * Get offices.
	 *
	 * @return Office[]
	 */
	public function get_offices() {
		return $this->offices;
	}

	/**
	 * User.
	 *
	 * @param string $code User code.
	 * @return User
	 */
	public function new_user( $code ) {
		if ( ! \array_key_exists( $code, $this->users ) ) {
			$user = new User( $code );

			$user->organisation = $this;

			$this->users[ $code ] = $user;
		}

		return $this->users[ $code ];
	}

	/**
	 * Office.
	 *
	 * @param string $code Office code.
	 * @return Office
	 */
	public function office( $code ) {
		if ( ! \array_key_exists( $code, $this->offices ) ) {
			$office = new Office( $code );

			$office->organisation = $this;

			$this->offices[ $code ] = $office;
		}

		return $this->offices[ $code ];
	}
}
