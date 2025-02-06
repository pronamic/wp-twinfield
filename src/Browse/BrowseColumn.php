<?php
/**
 * Browse column
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DOMDocument;
use DOMElement;

/**
 * Browse column class
 */
class BrowseColumn {
	/**
	 * Field.
	 * 
	 * @var string
	 */
	public $field;

	/**
	 * Operator.
	 * 
	 * `between`, `equal`, `none`.
	 * 
	 * @var string|null
	 */
	public $operator;

	/**
	 * From.
	 * 
	 * @var string|null
	 */
	public $from;

	/**
	 * To.
	 * 
	 * @var string|null
	 */
	public $to;

	/**
	 * Construct.
	 * 
	 * @param string $field Field.
	 */
	public function __construct( $field ) {
		$this->field = $field;
	}

	/**
	 * Between.
	 * 
	 * @param string $from From.
	 * @param string $to   To.
	 * @return self
	 */
	public function between( string $from, string $to ): self {
		$this->operator = 'between';
		$this->from     = $from;
		$this->to       = $to;

		return $this;
	}

	/**
	 * Between datetimes.
	 * 
	 * @param string $from From.
	 * @param string $to   To.
	 * @return self
	 */
	public function between_datetimes( DateTimeInterface $from, DateTimeInterface $to ): self {
		$timezone = new DateTimeZone( 'UTC' );

		return $this->between(
			DateTimeImmutable::createFromInterface( $from )->setTimezone( $timezone )->format( 'YmdHis' ),
			DateTimeImmutable::createFromInterface( $to )->setTimezone( $timezone )->format( 'YmdHis' )
		);
	}

	/**
	 * Equal.
	 * 
	 * @param string $vale Value.
	 * @return self
	 */
	public function equal( $value ) {
		$this->operator = 'equal';
		$this->from     = $value;
		$this->to       = '';

		return $this;
	}

	/**
	 * Convert to DOMElement.
	 * 
	 * @param DOMDocument $document
	 * @return DOMElement
	 */
	public function to_dom_element( DOMDocument $document ) {
		$column_element = $document->createElement( 'column' );

		$column_element->appendChild( $document->createElement( 'field', $this->field ) );

		if ( null !== $this->operator ) {
			$column_element->appendChild( $document->createElement( 'operator', $this->operator ) );
		}

		if ( null !== $this->from ) {
			$column_element->appendChild( $document->createElement( 'from', $this->from ) );
		}

		if ( null !== $this->to ) {
			$column_element->appendChild( $document->createElement( 'to', $this->to ) );
		}

		return $column_element;
	}
}
