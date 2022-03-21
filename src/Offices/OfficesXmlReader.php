<?php
/**
 * Offices XML Reader
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use Pronamic\WordPress\Twinfield\Organisations\Organisation;
use XMLReader;

/**
 * Offices XML Reader
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class OfficesXmlReader {
	/**
	 * Construct offices XML reader.
	 *
	 * @param Organisation $organisation Organisation.
	 */
	public function __construct( Organisation $organisation ) {
		$this->organisation = $organisation;
	}
	/**
	 * Create office list from XML.
	 *
	 * @param string $xml XML.
	 * @return Office[]
	 */
	public function read( $xml ) {
		$simplexml = \simplexml_load_string( $xml );

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
