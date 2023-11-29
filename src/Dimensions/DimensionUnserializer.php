<?php
/**
 * Dimension unserializer
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML/Articles
 */

namespace Pronamic\WordPress\Twinfield\Dimensions;

use Pronamic\WordPress\Twinfield\DimensionTypes;
use Pronamic\WordPress\Twinfield\Customers\CustomerUnserializer;
use Pronamic\WordPress\Twinfield\Organisations\Organisation;
use Pronamic\WordPress\Twinfield\Suppliers\SupplierUnserializer;
use Pronamic\WordPress\Twinfield\XML\Unserializer;
use Pronamic\WordPress\Twinfield\XML\Security;
use SimpleXMLElement;

/**
 * Dimension unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DimensionUnserializer extends Unserializer {
	/**
	 * Construct dimension unserializer.
	 * 
	 * @param Organisation $organisation Organisation.
	 */
	public function __construct( Organisation $organisation ) {
		$this->unserializers = [
			DimensionTypes::DEB => new CustomerUnserializer( $organisation ),
			DimensionTypes::CRD => new SupplierUnserializer( $organisation ),
		];
	}

	/**
	 * Unserialize the specified XML to an article.
	 *
	 * @param SimpleXMLElement $element the element to unserialize.
	 * @throws \Exception Throws exception when unserialize fails.
	 */
	public function unserialize( SimpleXMLElement $element ) {
		if ( 'dimension' !== $element->getName() ) {
			throw new \Exception(
				\sprintf(
					'Invalid element name: %s.',
					\esc_html( $element->getName() )
				)
			);
		}

		$type = (string) $element->type;

		if ( isset( $this->unserializers[ $type ] ) ) {
			$unserializer = $this->unserializers[ $type ];

			return $unserializer->unserialize( $element );
		}
	}
}
