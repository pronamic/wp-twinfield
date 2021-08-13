<?php
/**
 * Twinfield
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use Pronamic\WordPress\Twinfield\Organisations\Organisation;

/**
 * Twinfield
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Twinfield {
	/**
	 * Organisations.
	 *
	 * @var array
	 */
	private $organisations;

	/**
	 * Constructs and initializes a Twinfield object.
	 */
	public function __construct() {
		$this->organisations = $organisation;
	}

    public function new_organisation( $code ) {
        $organisation = new Organisation( $code );

        $this->organisations[ $code ] = $organisation;

        return $organisation;
    }
}
