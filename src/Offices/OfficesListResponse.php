<?php
/**
 * Offices list repsone
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use Pronamic\WordPress\Twinfield\Organisations\Organisation;

/**
 * Offices list repsone class
 */
final class OfficesListResponse {
	/**
	 * Organisation.
	 * 
	 * @var Organisation
	 */
	private Organisation $organisation;

	/**
	 * XML.
	 * 
	 * @var string
	 */
	private string $xml;

	/**
	 * Construct offices list response.
	 * 
	 * @param Organisation $organisation Organisation.
	 * @param string       $xml          XML.
	 */
	public function __construct( Organisation $organisation, string $xml ) {
		$this->organisation = $organisation;
		$this->xml          = $xml;
	}

	/**
	 * To offices.
	 * 
	 * @return Office[]
	 */
	public function to_offices() {
		$simplexml = \simplexml_load_string( $this->xml );

		$offices = [];

		foreach ( $simplexml->office as $office_xml ) {
			$office = $this->organisation->office( (string) $office_xml );

			$office->set_name( (string) $office_xml['name'] );
			$office->set_shortname( (string) $office_xml['shortname'] );

			$offices[] = $office;
		}

		return $offices;
	}
}
