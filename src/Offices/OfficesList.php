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

/**
 * Offices List
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class OfficesList implements IteratorAggregate, JsonSerializable {
	private $organisation;

	private $offices = array();

    public function getIterator() {
        return new ArrayIterator( $this->offices );
    }

 	public function jsonSerialize() {
        return $this->offices;
    }

	public static function from_xml( $xml, $organisation ) {
		$simplexml = \simplexml_load_string( $xml );

		$list = new self();

		foreach ( $simplexml->office as $element ) {
			$office = $organisation->new_office( (string) $element );

			$office->set_name( (string) $element['name'] );
			$office->set_shortname( (string) $element['shortname'] );

			$list->offices[] = $office;
		}

		return $list;
	}
}
