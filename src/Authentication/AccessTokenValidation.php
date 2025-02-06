<?php
/**
 * Access Token Validation.
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Authentication;

use DateTimeInterface;
use DateTimeImmutable;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Organisations\Organisation;
use Pronamic\WordPress\Twinfield\Users\User;

/**
 * Access Token Validation.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class AccessTokenValidation implements JsonSerializable {
	/**
	 * Expiration timestamp.
	 *
	 * @var int
	 */
	private $expiration;

	/**
	 * Organisation.
	 * 
	 * @var Organisation
	 */
	public Organisation $organisation;

	/**
	 * User.
	 * 
	 * @var User
	 */
	public User $user;

	/**
	 * Cluster URL.
	 * 
	 * @var string
	 */
	public string $cluster_url;

	/**
	 * Construct access token validation object.
	 * 
	 * @param int          $expiration   Expiration timestamp.
	 * @param Organisation $organisation Organisation.
	 * @param User         $user         User.
	 * @param string       $cluster_url  Cluster URL.
	 */
	public function __construct( $expiration, Organisation $organisation, User $user, $cluster_url ) {
		$this->expiration   = $expiration;
		$this->organisation = $organisation;
		$this->user         = $user;
		$this->cluster_url  = $cluster_url;
	}

	/**
	 * Get expiration date/time.
	 * 
	 * @return DateTimeImmutable
	 */
	public function get_expiration_datetime() {
		return new \DateTimeImmutable( '@' . $this->expiration );
	}

	/**
	 * Check if the access token is expired.
	 * 
	 * @return bool True if expired, false otherwise.
	 */
	public function is_expired() {
		return $this->expires_in( 0 );
	}

	/**
	 * Check if the access token expires in the specified number seconds.
	 * 
	 * @param int $seconds Seconds.
	 * @return bool True if expires in the number seconds, false otherwise.
	 */
	public function expires_in( $seconds = 0 ) {
		return $this->expiration < ( \time() + $seconds );
	}

	/**
	 * JSON serialize.
	 * 
	 * @return object
	 */
	public function jsonSerialize() {
		return (object) [
			'exp'                      => $this->expiration,
			'twf.organisationCode'     => $this->organisation->get_code(),
			'twf.organisationId'       => $this->organisation->get_uuid(),
			'twf.organisationUserCode' => $this->user->get_code(),
			'twf.clusterUrl'           => $this->cluster_url,
		];
	}

	/**
	 * Create access token validation object from a plain object.
	 * 
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_object( $value ) {
		$organisation = new Organisation( $value->{'twf.organisationCode'} );
		$organisation->set_uuid( $value->{'twf.organisationId'} );

		$user = $organisation->new_user( $value->{'twf.organisationUserCode'} );

		$cluster_url = $value->{'twf.clusterUrl'};

		$result = new self( $value->exp, $organisation, $user, $cluster_url );

		return $result;
	}
}
