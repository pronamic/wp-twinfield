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

	use CodeTrait;

	use NameTrait;

	use ShortnameTrait;

	use UuidTrait;

	private $users;

	private $offices;

	public function __construct( $code ) {
		$this->set_code( $code );

		$this->twinfield = new Twinfield();

		$this->users   = [];
		$this->offices = [];
	}

	public function get_twinfield() {
		return $this->twinfield;
	}

	public function get_offices() {
		return $this->offices;
	}

	public function new_user( $code ) {
		if ( ! \array_key_exists( $code, $this->users ) ) {
			$user = new User( $code );

			$user->organisation = $this;

			$this->users[ $code ] = $user;
		}

		return $this->users[ $code ];
	}

	public function office( $code ) {
		return $this->new_office( $code );
	}

	public function new_office( $code ) {
		if ( ! \array_key_exists( $code, $this->offices ) ) {
			$this->offices[ $code ] = new Office( $this, $code );
		}

		return $this->offices[ $code ];
	}
}
