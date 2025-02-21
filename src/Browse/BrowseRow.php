<?php
/**
 * Browse row
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use DOMElement;

/**
 * Browse row class
 */
class BrowseRow {
	/**
	 * Data.
	 * 
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/td
	 * @var array
	 */
	public $data = [];

	/**
	 * Key.
	 * 
	 * @var BrowseRowKey
	 */
	public $key;

	/**
	 * Construct.
	 * 
	 * @param BrowseRowKey $key Key.
	 */
	public function __construct( BrowseRowKey $key ) {
		$this->key = $key;
	}

	/**
	 * From DOMElement.
	 * 
	 * @param DOMElement $element Element.
	 * @return self
	 */
	public static function from_dom_element( DOMElement $element ): self {
		if ( 'tr' !== $element->tagName ) {
			throw new \Exception( 'No tr element.' );
		}

		$key_element = $element->getElementsByTagName( 'key' )->item( 0 );

		if ( null === $key_element ) {
			throw new \Exception( 'No key element.' );
		}

		$row = new self( BrowseRowKey::from_dom_element( $key_element ) );

		return $row;
	}
}
