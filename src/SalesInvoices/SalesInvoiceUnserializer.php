<?php
/**
 * Sales invoices unserializer
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML/Articles
 */

namespace Pronamic\WordPress\Twinfield\SalesInvoices;

use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\XML\Unserializer;
use Pronamic\WordPress\Twinfield\XML\DateUnserializer;
use Pronamic\WordPress\Twinfield\VatCode;

/**
 * Sales invoices unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SalesInvoiceUnserializer extends Unserializer {
	/**
	 * Constructs and initializes an sales invoice unserializer.
	 */
	public function __construct() {
		$this->date_unserializer = new DateUnserializer();
	}

	/**
	 * Get inner XML of DOMNode.
	 *
	 * @link https://www.php.net/manual/en/domdocument.savexml.php
	 * @link https://stackoverflow.com/questions/2087103/how-to-get-innerhtml-of-domnode
	 * @param \DOMNode $node DOM node.
	 * @return string
	 */
	private function get_inner_xml( \DOMNode $node = null ) {
		if ( null === $node ) {
			return null;
		}

		$child_nodes_array = \iterator_to_array( $node->childNodes );

		$child_nodes_xml = array_map(
			function ( $node ) {
				return $node->ownerDocument->saveXML( $node );
			},
			$child_nodes_array
		);

		$inner_xml = implode( $child_nodes_xml );

		return $inner_xml;
	}

	/**
	 * Unserialize the specified XML to an article.
	 *
	 * @param string $xml The XML element to unserialize.
	 * @throws \Exception Throws exception when unserialize of XML fails.
	 * @throws \Pronamic\WordPress\Twinfield\XML\XmlPostErrors Throws XML posts errors exception when 'result' attribute is '0'.
	 */
	public function unserialize( string $xml ) {
		$simplexml = \simplexml_load_string( $xml );

		if ( false === $simplexml ) {
			throw new \Exception( 'Could not load XML string in SimpleXMLElement object.' );
		}

		if ( 'salesinvoice' !== $simplexml->getName() ) {
			throw new \Exception(
				\sprintf(
					'Invalid element name: %s.',
					\esc_html( $simplexml->getName() )
				)
			);
		}

		/**
		 * Problem tags.
		 *
		 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Types/XmlWebServices#Parsing-results
		 */
		if ( '0' === (string) $simplexml['result'] ) {
			throw new \Pronamic\WordPress\Twinfield\XML\XmlPostErrors( $simplexml );
		}

		/**
		 * Ok.
		 */
		$sales_invoice = new SalesInvoice();

		$header = $sales_invoice->get_header();

		$dom_header = \dom_import_simplexml( $simplexml->header );

		if ( $simplexml->header ) {
			$header->set_office( Security::filter( $simplexml->header->office ) );
			$header->set_type( Security::filter( $simplexml->header->invoicetype ) );
			$header->set_number( Security::filter( $simplexml->header->invoicenumber ) );
			$header->set_date( $this->date_unserializer->unserialize( $simplexml->header->invoicedate ) );
			$header->set_due_date( $this->date_unserializer->unserialize( $simplexml->header->duedate ) );
			$header->set_bank( Security::filter( $simplexml->header->bank ) );
			$header->set_customer( Security::filter( $simplexml->header->customer ) );
			$header->set_status( Security::filter( $simplexml->header->status ) );

			/**
			 * Header and footer text can container <br /> elements.
			 *
			 * @link https://stackoverflow.com/questions/4145424/getting-the-xml-content-of-a-simplexmlelement
			 */
			$header->set_header_text( Security::filter( $this->get_inner_xml( $dom_header->getElementsByTagName( 'headertext' )->item( 0 ) ) ) );
			$header->set_footer_text( Security::filter( $this->get_inner_xml( $dom_header->getElementsByTagName( 'footertext' )->item( 0 ) ) ) );

			$header->set_invoice_address_number( Security::filter( $simplexml->header->invoiceaddressnumber, FILTER_VALIDATE_INT ) );
			$header->set_deliver_address_number( Security::filter( $simplexml->header->deliveraddressnumber, FILTER_VALIDATE_INT ) );
		}

		if ( $simplexml->lines ) {
			foreach ( $simplexml->lines->line as $element_line ) {
				$line = $sales_invoice->new_line();

				$line->set_id( Security::filter( $element_line['id'] ) );
				$line->set_article( Security::filter( $element_line->article ) );
				$line->set_subarticle( Security::filter( $element_line->subarticle ) );
				$line->set_quantity( Security::filter( $element_line->quantity ) );
				$line->set_units( Security::filter( $element_line->units ) );
				$line->set_allow_discount_or_premium( Security::filter( $element_line->units, FILTER_VALIDATE_BOOLEAN ) );
				$line->set_description( Security::filter( $element_line->description ) );
				$line->set_value_excl( Security::filter( $element_line->valueexcl, FILTER_VALIDATE_FLOAT ) );
				$line->set_vat_value( Security::filter( $element_line->vatvalue, FILTER_VALIDATE_FLOAT ) );
				$line->set_value_inc( Security::filter( $element_line->valueinc, FILTER_VALIDATE_FLOAT ) );
				$line->set_free_text_1( Security::filter( $element_line->freetext1 ) );
				$line->set_free_text_2( Security::filter( $element_line->freetext2 ) );
				$line->set_free_text_3( Security::filter( $element_line->freetext3 ) );
				$line->set_performance_type( Security::filter( $element_line->performancetype ) );
				$line->set_performance_date( $this->date_unserializer->unserialize( $element_line->performancedate ) );
			}
		}

		if ( $simplexml->vatlines ) {
			foreach ( $simplexml->vatlines->vatline as $element_line ) {
				$vat_line = $sales_invoice->new_vat_line();

				// VAT code.
				$vat_code = new VatCode(
					Security::filter( $element_line->vatcode ),
					Security::filter( $element_line->vatcode['name'] ),
					Security::filter( $element_line->vatcode['shortname'] )
				);

				$vat_line->set_vat_code( $vat_code );

				$vat_line->set_vat_value( Security::filter( $element_line->vatvalue, FILTER_VALIDATE_FLOAT ) );
			}
		}

		if ( $simplexml->totals ) {
			$totals = $sales_invoice->get_totals();

			$totals->set_value_excl( Security::filter( $simplexml->totals->valueexcl, FILTER_VALIDATE_FLOAT ) );
			$totals->set_value_inc( Security::filter( $simplexml->totals->valueinc, FILTER_VALIDATE_FLOAT ) );
		}

		// Response.
		return $sales_invoice;
	}
}
