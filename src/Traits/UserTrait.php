<?php

namespace Pronamic\WordPress\Twinfield\Traits;

use Pronamic\WordPress\Twinfield\Users\User;

trait UserTrait {
	/**
	 * User.
	 *
	 * @var User|null
	 */
	private $user;

	/**
	 * Get user.
	 *
	 * @return User|null
	 */
	public function get_user() {
		return $this->user;
	}

	/**
	 * Set user.
	 *
	 * @param User|null $user User.
	 */
	public function set_user( $user ) {
		$this->user = $user;
	}
}
