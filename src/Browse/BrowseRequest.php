<?php
/**
 * Browse request
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use DOMDocument;

/**
 * Browse request class
 */
class BrowseRequest {
	/**
	 * Code.
	 * 
	 * @var string
	 */
	public $code;

	/**
	 * Optimize.
	 * 
	 * Enables or disables optimization of the output. Possible values are
	 * `true` and `false`. `false` is the default value. When set to `true`,
	 * the `field`, `hideforuser` and `type` attributes are removed from all
	 * `<td>` elements except those in the `<th>` element.
	 *
	 * @var bool|null
	 */
	public $optimize;

	/**
	 * Output format.
	 * 
	 * Determines the output format of the data. Possible values are "XML" and
	 * "CSVEXCEL", "XML" is the default value. Output format XML creates a
	 * series of `<tr>` and `<td>` elements to store the data. Output format
	 * CSVEXCEL creates a single `<csv>` element containing CSV data optimized
	 * for use in MS Excel. The field separator is the tab character.
	 * 
	 * @var string|null
	 */
	public $output_format;

	/**
	 * Sort fields.
	 * 
	 * In the browse request it is possible to add up to three sort fields.
	 * The data of the XML response will then be sorted based on these fields. 
	 *
	 * @var array
	 */
	public $sort_fields = [];

	/**
	 * Columns.
	 *
	 * @var BrowseColumn[]
	 */
	private $columns = [];

	/**
	 * Construct browse request.
	 * 
	 * @param string $code Code.
	 */
	public function __construct( $code ) {
		$this->code = $code;
	}

	/**
	 * Optimize.
	 * 
	 * @param bool|null $optimize Optimize.
	 * @return self
	 */
	public function optimize( $optimize = true ) {
		$this->optimize = $optimize;

		return $this;
	}

	/**
	 * Column.
	 * 
	 * @param BrowseColumn $column Column.
	 * @return self
	 */
	public function column( BrowseColumn $column ) {
		$this->columns[] = $column;

		return $this;
	}

	/**
	 * Convert to DOMDocument.
	 * 
	 * @return DOMDocument
	 */
	public function to_dom_document() {
		$document = new DOMDocument();

		$columns_element = $document->appendChild( $document->createElement( 'columns' ) );

		$columns_element->setAttribute( 'code', $this->code );

		if ( null !== $this->optimize ) {
			$columns_element->setAttribute( 'optimize', $this->optimize ? 'true' : 'false' );
		}

		if ( null !== $this->output_format ) {
			$columns_element->setAttribute( 'outputformat', $this->output_format );
		}

		if ( \count( $this->sort_fields ) > 0 ) {
			$sort_element = $columns_element->appendChild( $document->createElement( 'sort' ) );

			foreach ( $this->sort_fields as $sort_field ) {

			}
		}

		foreach ( $this->columns as $column ) {
			$columns_element->appendChild( $column->to_dom_element( $document ) );
		}

		return $document;
	}

	/**
	 * Convert to XML string.
	 * 
	 * @return string
	 */
	public function to_xml() {
		$dom = $this->to_dom_document();

		return $dom->saveXML( $dom->documentElement );
	}

	/**
	 * String.
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->to_xml();
	}
}
