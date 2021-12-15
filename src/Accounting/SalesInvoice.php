<?php
/**
 * Sales Invoice
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Accounting;

use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\ProcessXmlString;
use Pronamic\WordPress\Twinfield\XMLProcessor;
use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Sales Invoice
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SalesInvoice {
	public function from_xml( $xml, $organisation ) {
		$simplexml = \simplexml_load_string( $xml );

		$object = new self();

		$office = $organisation->office( (string) $simplexml->header->office );

		$sales_invoice_type = $office->sales_invoice_type( (string) $simplexml->header->invoicetype );

		$object->header = (object) array(
			'office'                 => $office,
			'invoice_type'           => $sales_invoice_type,
			'invoice_number'         => (string) $simplexml->header->invoicenumber,
			'invoice_date'           => \DateTimeImmutable::createFromFormat( 'Ymd', (string) $simplexml->header->invoicedate ),
			'due_date'               => \DateTimeImmutable::createFromFormat( 'Ymd', (string) $simplexml->header->duedate ),
			'bank'                   => (string) $simplexml->header->bank,
			'invoice_address_number' => (string) $simplexml->header->invoiceaddressnumber,
			'deliver_address_number' => (string) $simplexml->header->deliveraddressnumber,
			'customer'               => (string) $simplexml->header->customer,
		);

		$object->lines = array();

		foreach ( $simplexml->lines->line as $element_line ) {
			$object->lines[] = (object) array(
				'id'          => (string) $element_line['id'],
				'article'     => (string) $element_line->article,
				'subarticle'  => (string) $element_line->subarticle,
				'quantity'    => (string) $element_line->quantity,
				'units'       => (string) $element_line->units,
				'description' => (string) $element_line->description,
				'free_text_1' => (string) $element_line->freetext1,
				'free_text_2' => (string) $element_line->freetext2,
				'free_text_3' => (string) $element_line->freetext3,
			);
		}

		return $object;
	}
}
