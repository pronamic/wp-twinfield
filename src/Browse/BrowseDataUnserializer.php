<?php
/**
 * Browse data unserializer
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use Pronamic\WordPress\Twinfield\Organisations\Organisation;
use Pronamic\WordPress\Twinfield\Transactions\TransactionLine;
use Pronamic\WordPress\Twinfield\XML\DateTimeUnserializer;
use SimpleXMLElement;

/**
 * Browse data unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class BrowseDataUnserializer {
	/**
	 * Construct browse data unserializer.
	 *
	 * @param Organisation $organisation Organisation.
	 */
	public function __construct( Organisation $organisation ) {
		$this->organisation = $organisation;
	}

	/**
	 * Unserialize.
	 * 
	 * @param string $value XML.
	 * @return array
	 */
	public function unserialize( $value ) {
		$data = [];

		$simplexml = \simplexml_load_string( $value );

		foreach ( $simplexml->tr as $tr ) {
			$office = $this->organisation->office( (string) $tr->key->office );

			$type = $office->new_transaction_type( (string) $tr->key->code );

			$transaction = $type->new_transaction( (string) $tr->key->number );

			$line = $transaction->new_line( (string) $tr->key->line );

			$index = 0;

			foreach ( $simplexml->th->td as $td ) {
				$this->unserialize_td( (string) $td, $tr->td[ $index ], $line );

				++$index;
			}

			$data[] = $line;
		}

		return $data;
	}

	/**
	 * Unserialize table data.
	 *
	 * @param string           $key     Key.
	 * @param SimpleXMLElement $element SimpleXmlElement.
	 * @param TransactionLine  $line    Line.
	 *
	 * @return mixed|void
	 */
	private function unserialize_td( $key, SimpleXMLElement $element, TransactionLine $line ) {
		switch ( $key ) {
			case 'fin.trs.head.modified':
				return $line->get_transaction()->get_header()->set_modification_date( \DateTimeImmutable::createFromFormat( 'YmdHis', (string) $element, new \DateTimeZone( 'UTC' ) ) );
			case 'fin.trs.line.basevaluesigned':
				return $line->set_base_value( (string) $element );
			case 'fin.trs.line.openbasevaluesigned':
				return null;
			case 'fin.trs.line.valuesigned':
				return $line->set_value( (string) $element );
			case 'fin.trs.line.debitcredit':
				return $line->set_debit_credit( (string) $element );
			case 'fin.trs.line.dim1':
				return $line->set_dimension_1( (string) $element );
			case 'fin.trs.line.dim2':
				return null;
			case 'fin.trs.line.dim3':
				return null;
			case 'fin.trs.line.description':
				return $line->set_description( (string) $element );
			case 'fin.trs.line.invnumber':
				return $line->set_invoice_number( (string) $element );
			case 'fin.trs.line.matchdate':
				$value = (string) $element;

				if ( '' === $value ) {
					return null;
				}

				return $line->set_match_date( \DateTimeImmutable::createFromFormat( 'Ymd', $value, new \DateTimeZone( 'UTC' ) )->setTime( 0, 0 ) );
		}
	}
}
