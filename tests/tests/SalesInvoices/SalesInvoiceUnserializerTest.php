<?php
/**
 * Sales invoice unserializer test
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WP/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\SalesInvoices;

use PHPUnit\Framework\TestCase;

/**
 * Sales invoice unserializer test
 *
 * @since      1.0.0
 * @package    Pronamic/WP/Twinfield.SalesInvoices
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SalesInvoiceUnserializerTest extends TestCase {
	/**
	 * Test errors exception.
	 */
	public function test_errors_exception() {
		$string = file_get_contents( __DIR__ . '/../../xml/SalesInvoices/insert-sales-invoice-customer-error.xml', true );

		$unserializer = new SalesInvoiceUnserializer();

		$this->expectException( \Pronamic\WordPress\Twinfield\XML\XmlPostErrors::class );

		$sales_invoice = $unserializer->unserialize( $string );
	}

	/**
	 * Test errors exception problem elements.
	 */
	public function test_errors_exception_problem_elements() {
		$string = file_get_contents( __DIR__ . '/../../xml/SalesInvoices/insert-sales-invoice-customer-error.xml', true );

		$unserializer = new SalesInvoiceUnserializer();

		try {
			$sales_invoice = $unserializer->unserialize( $string );

			$this->fail( 'XML post errors exception was not thrown.' );
		} catch ( \Pronamic\WordPress\Twinfield\XML\XmlPostErrors $errors ) {
			$problem_elements = $errors->get_problem_elements();

			$this->assertCount( 15, $problem_elements );
		}
	}
}
