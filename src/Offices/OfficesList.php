<?php
/**
 * Offices List
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Organisations\Organisation;

/**
 * Offices List
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class OfficesList implements IteratorAggregate, JsonSerializable {
	/**
	 * Offices.
	 *
	 * @var Office[]
	 */
	private $offices = [];

	/**
	 * Get iterator.
	 *
	 * @return ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator( $this->offices );
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed|Office[]
	 */
	public function jsonSerialize() {
		return $this->offices;
	}

	/**
	 * Create office list from XML.
	 *
	 * @param string       $xml          XML.
	 * @param Organisation $organisation Organisation.
	 *
	 * @return OfficesList
	 */
	public static function from_xml( $xml, $organisation ) {
		$simplexml = \simplexml_load_string( $xml );

		$list = new self();

		foreach ( $simplexml->office as $element ) {
			$office = $organisation->office( (string) $element );

			$office->set_name( (string) $element['name'] );
			$office->set_shortname( (string) $element['shortname'] );

			$list->offices[] = $office;
		}

		return $list;
	}
}
