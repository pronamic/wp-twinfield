<?php
/**
 * Supplier unserializer
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML/Articles
 */

namespace Pronamic\WordPress\Twinfield\XML\Suppliers;

use Pronamic\WordPress\Twinfield\DimensionTypes;
use Pronamic\WordPress\Twinfield\Suppliers\Supplier;
use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\XML\Unserializer;

/**
 * Sales invoices unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SupplierUnserializer extends Unserializer {
	/**
	 * Unserialize the specified XML to an article.
	 *
	 * @param \SimpleXMLElement $element The XML element to unserialize.
	 */
	public function unserialize( \SimpleXMLElement $element ) {
		if ( 'dimension' === $element->getName() && DimensionTypes::CRD === Security::filter( $element->type ) ) {
			$supplier = new Supplier(
				Security::filter( $element->type ),
				Security::filter( $element->code ),
			);

			$supplier->set_office( Security::filter( $element->office ) );
			$supplier->set_code( Security::filter( $element->code ) );
			$supplier->set_name( Security::filter( $element->name ) );
			$supplier->set_shortname( Security::filter( $element->shortname ) );

			return $supplier;
		}
	}
}
