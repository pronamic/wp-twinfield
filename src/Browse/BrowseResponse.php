<?php
/**
 * Browse response
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use DOMDocument;
use DOMElement;

/**
 * Browse response class
 */
class BrowseResponse {
	/**
	 * Result.
	 * 
	 * @var bool
	 */
	public $result;

	/**
	 * First.
	 * 
	 * @var int
	 */
	public $first;

	/**
	 * Last.
	 * 
	 * @var int
	 */
	public $last;

	/**
	 * Total.
	 * 
	 * @var int
	 */
	public $total;

	/**
	 * Header.
	 * 
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/th
	 * @var array
	 */
	public $header;

	/**
	 * Rows.
	 * 
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/tr
	 * @var BrowseRow[]
	 */
	public $rows = [];

	/**
	 * From XML.
	 * 
	 * @param string $xml XML.
	 * @return self
	 */
	public static function from_xml( $xml ): self {
		$document = new DOMDocument();

		$result = $document->loadXML( $xml );

		if ( false === $result ) {
			throw new \Exception( 'Could not load browse response from XML.' );
		}

		return self::from_dom_document( $document );
	}

	/**
	 * From DOMDocument.
	 * 
	 * @param DOMDocument $document Document.
	 * @return self
	 */
	public static function from_dom_document( DOMDocument $document ): self {
		if ( null === $document->documentElement ) {
			throw new \Exception( 'No first document element.' );
		}

		return self::from_dom_element( $document->documentElement );
	}

	/**
	 * From DOMElement.
	 * 
	 * @param DOMElement $element Element.
	 * @return self
	 */
	public static function from_dom_element( DOMElement $element ): self {
		if ( 'browse' !== $element->tagName ) {
			throw new \Exception( 'No browse element.' );
		}

		$response = new self();

		$response->result = ( '1' === $element->getAttribute( 'result' ) );
		$response->first  = (int) $element->getAttribute( 'first' );
		$response->last   = (int) $element->getAttribute( 'last' );
		$response->total  = (int) $element->getAttribute( 'total' );

		$th = $element->getElementsByTagName( 'th' )->item( 0 );

		if ( null === $th ) {
			throw new \Exception( 'No header element.' );
		}

		$trs = $element->getElementsByTagName( 'tr' );

		foreach ( $trs as $tr ) {
			$response->rows[] = BrowseRow::from_dom_element( $tr );
		}

		return $response;
	}
}
