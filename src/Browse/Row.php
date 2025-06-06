<?php
/**
 * Row
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

/**
 * Row
 *
 * This class represents a Twinfield browse data row.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Row implements \Stringable {
	/**
	 * XML definition.
	 *
	 * @var \SimpleXMLElement
	 */
	private $xml_definition;

	/**
	 * Data.
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Constructs and initialize a Twinfield browse data object.
	 *
	 * @param \SimpleXMLElement $xml_definition XML definition.
	 */
	public function __construct( \SimpleXMLElement $xml_definition ) {
		$this->xml_definition = $xml_definition;
		$this->data           = [];

		$this->parse_data();
	}

	/**
	 * Parse rows.
	 */
	private function parse_data() {
		$this->data = [];

		foreach ( $this->xml_definition->td as $td ) {
			$field = (string) $td['field'];

			$this->data[ $field ] = (string) $td;
		}
	}

	/**
	 * Get XML key.
	 *
	 * @return \SimpleXMLElement
	 */
	public function get_xml_key() {
		return $this->xml_definition->key;
	}

	/**
	 * Get rows.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Check if this row has the specified field.
	 *
	 * @param string $field The field.
	 * @return boolean true if field exists, false otherwise.
	 */
	public function has_field( $field ) {
		return isset( $this->data[ $field ] );
	}

	/**
	 * Get field.
	 *
	 * @param string $field The field.
	 * @return string
	 */
	public function get_field( $field ) {
		if ( isset( $this->data[ $field ] ) ) {
			return $this->data[ $field ];
		}
	}

	/**
	 * Create a string representatino of this object.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return (string) $this->xml_definition->asXML();
	}
}
