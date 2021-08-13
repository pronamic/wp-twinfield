<?php
/**
 * List request
 *
 * @since      1.0.0
 * @see        https://c3.twinfield.com/webservices/documentation/#/GettingStarted/WebServicesOverview#List-entities
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Session
 *
 * This class represents an Twinfield session.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ListRequest {
	/**
	 * Specify what type of data to read.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Specify from wich office to read.
	 *
	 * @var string
	 */
	private $office;

	/**
	 * Constructs and initialize an Twinfield read request.
	 *
	 * @param string $type    Specify what type of data to read.
	 * @param string $office  Specify from wich office to read.
	 */
	public function __construct( $type, $office ) {
		$this->type   = $type;
		$this->office = $office;
	}

	/**
	 * Get the read request type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get the read request office.
	 *
	 * @return string
	 */
	public function get_office() {
		return $this->office;
	}
}
