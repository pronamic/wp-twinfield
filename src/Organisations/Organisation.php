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

/**
 * Organisation
 *
 * This class represents a Twinfield organisation
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Organisation extends CodeName implements \JsonSerializable {
    /**
     * Twinfield.
     * 
     * @var Twinfield
     */
    private $twinfield;

    private $users;

    private $offices;

    use UuidTrait;

    public function __construct( $code ) {
        parent::__construct( $code );

        $this->users     = array();
        $this->offices   = array();
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
            $office = new Office( $code );

            $office->organisation = $this;

            $this->offices[ $code ] = $office;
        }

        return $this->offices[ $code ];
    }

    public function jsonSerialize() {
        return (object) array(
            'code'      => $this->get_code(),
            'name'      => $this->get_name(),
            'shortname' => $this->get_shortname(),
        );
    }
}
