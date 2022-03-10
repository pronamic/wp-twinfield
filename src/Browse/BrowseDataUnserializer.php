<?php
/**
 * Browse data unserializer
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Browse;

use DOMDocument;
use DOMNode;
use Pronamic\WordPress\Twinfield\Organisations\Organisation;
use Pronamic\WordPress\Twinfield\Transactions\TransactionLine;

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
	 * Get first element.
	 *
	 * @param DOMNode $node Node.
	 * @param string  $name Name.
	 *
	 * @return DOMNode
	 */
	private function get_element( DOMNode $node, $name ) {
		$item = $node->getElementsByTagName( $name )->item( 0 );

		if ( null === $item ) {
			throw new \Eception( 'Could not find element.' );
		}

		return $item;
	}

	/**
	 * Unserialize.
	 * 
	 * @param \SimpleXMLElement $element Element.
	 * @return array
	 */
	public function unserialize( $string ) {
		$data = [];

		$document = new DOMDocument();

		$document->loadXML( $string );

		$th = $this->get_element( $document, 'th' );

		foreach ( $document->getElementsByTagName( 'tr' ) as $tr ) {
			$key = $this->get_element( $tr, 'key' );
			
			$office = $this->organisation->office( $this->get_element( $key, 'office' )->nodeValue );

			$type = $office->new_transaction_type( $this->get_element( $key, 'code' )->nodeValue );

			$transaction = $type->new_transaction( $this->get_element( $key, 'number' )->nodeValue );

			$line = $transaction->new_line( $this->get_element( $key, 'line' )->nodeValue );

			$tds = $tr->getElementsByTagName( 'td' );

			foreach ( $th->getElementsByTagName( 'td' ) as $i => $td ) {
				$this->unserialize_td( $td->nodeValue, $tds->item( $i ), $line );
			}

			$data[] = $line;
		}

		return $data;
	}

	/**
	 * Unserialize table data.
	 *
	 * @param string          $key  Key.
	 * @param DOMNode         $node Node.
	 * @param TransactionLine $line Line.
	 *
	 * @return mixed|void
	 */
	private function unserialize_td( $key, DOMNode $node, TransactionLine $line ) {
		switch ( $key ) {
			case 'fin.trs.line.basevaluesigned':
				return $line->set_base_value( $node->nodeValue );
			case 'fin.trs.line.openbasevaluesigned':
				return $line->set_open_base_value( $node->nodeValue );
			case 'fin.trs.line.valuesigned':
				return $line->set_value( $node->nodeValue );
			case 'fin.trs.line.debitcredit':
				return $line->set_debit_credit( $node->nodeValue );
			case 'fin.trs.line.openbasevaluesigned':
				return $line->set_open_base_value( $node->nodeValue );
			case 'fin.trs.line.dim1':
				return $line->set_dimension_1( $node->nodeValue );
			case 'fin.trs.line.dim2':
				return $line->set_dimension_2( $node->nodeValue );
			case 'fin.trs.line.dim3':
				return $line->set_dimension_3( $node->nodeValue );
			case 'fin.trs.line.description':
				return $line->set_description( $node->nodeValue );
			case 'fin.trs.line.invnumber':
				return $line->set_invoice_number( $node->nodeValue );
		}
	}
}
