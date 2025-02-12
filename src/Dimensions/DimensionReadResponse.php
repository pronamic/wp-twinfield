<?php
/**
 * Dimension read response
 *
 * @package Pronamic/WordPress/Twinfield/Dimensions
 */

namespace Pronamic\WordPress\Twinfield\Dimensions;

use Pronamic\WordPress\Twinfield\Organisations\Organisation;

/**
 * Dimension read response class
 */
class DimensionReadResponse {
	/**
	 * XML.
	 * 
	 * @var string
	 */
	public $xml;

	/**
	 * Organisation.
	 * 
	 * @var Organisation
	 */
	public $organisation;

	/**
	 * Construct dimension read response.
	 *
	 * @param string       $xml          XML.
	 * @param Organisation $organisation Organisation.
	 */
	public function __construct( $xml, $organisation ) {
		$this->xml = $xml;

		$this->organisation = $organisation;
	}

	/**
	 * Dimension.
	 * 
	 * @return Dimension
	 */
	public function dimension() {
		return Dimension::from_xml( $this->xml, $this->organisation );
	}
}
