<?php
/**
 * Browse row key
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use DOMDocument;
use DOMElement;

/**
 * Browse row key class
 */
class BrowseRowKey {
	/**
	 * Office.
	 * 
	 * @var string
	 */
	public $office;

	/**
	 * Code.
	 * 
	 * @var string
	 */
	public $code;

	/**
	 * Number.
	 * 
	 * @var string
	 */
	public $number;

	/**
	 * Line.
	 * 
	 * @var string
	 */
	public $line;

	/**
	 * Construct browse row key.
	 * 
	 * @param string $office Office.
	 * @param string $code   Code.
	 * @param string $number Number.
	 * @param string $line   Line.
	 */
	public function __construct( $office, $code, $number, $line ) {
		$this->office = $office;
		$this->code   = $code;
		$this->number = $number;
		$this->line   = $line;
	}

	/**
	 * From DOMElement.
	 * 
	 * @param DOMElement $element Element.
	 * @return self
	 */
	public static function from_dom_element( DOMElement $element ): self {
		if ( 'key' !== $element->tagName ) {
			throw new \Exception( 'No key element.' );
		}

		$office_element = $element->getElementsByTagName( 'office' )->item( 0 );

		if ( null === $office_element ) {
			throw new \Exception( 'No office element.' );
		}

		$code_element = $element->getElementsByTagName( 'code' )->item( 0 );

		if ( null === $code_element ) {
			throw new \Exception( 'No code element.' );
		}

		$number_element = $element->getElementsByTagName( 'number' )->item( 0 );

		if ( null === $number_element ) {
			throw new \Exception( 'No number element.' );
		}

		$line_element = $element->getElementsByTagName( 'line' )->item( 0 );

		if ( null === $line_element ) {
			throw new \Exception( 'No line element.' );
		}

		return new self(
			$office_element->nodeValue,
			$code_element->nodeValue,
			$number_element->nodeValue,
			$line_element->nodeValue
		);
	}
}
