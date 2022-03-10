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

/**
 * Browse data unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class BrowseDataUnserializer {
	public function __construct( $organisation ) {
		$this->organisation = $organisation;
	}

	private function get_element( $node, $name ) {
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

			$transaction_type = $office->new_transaction_type( $this->get_element( $key, 'code' )->nodeValue );

			$transaction = $transaction_type->new_transaction( $this->get_element( $key, 'number' )->nodeValue );

			$transaction_line = $transaction->new_line($this->get_element( $key, 'line' )->nodeValue );

			$tds = $tr->getElementsByTagName( 'td' );

			foreach ( $th->getElementsByTagName( 'td' ) as $i => $td ) {
				$this->unserialize_td( $td->nodeValue, $tds->item( $i ), $transaction_line );
			}

			$data[] = $transaction_line;
		}

		return $data;
	}

	private function unserialize_td( $key, $node, $transaction_line ) {
		if ( 'fin.trs.line.basevaluesigned' === $key ) {
			$transaction_line->set_value( $node->nodeValue );
		}

		if ( 'fin.trs.line.openbasevaluesigned' === $key ) {
			$transaction_line->open_base_value = $node->nodeValue;
		}
	}
}
