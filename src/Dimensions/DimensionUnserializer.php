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
	 * Organisation.
	 * 
	 * @var Organisation
	 */
	public $organisation;

	/**
	 * Construct dimension unserializer.
	 * 
	 * @param Organisation $organisation Organisation.
	 */
	public function __construct( Organisation $organisation ) {
		$this->organisation = $organisation;

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

		if ( '0' === (string) $element['result'] ) {
			if ( '0' === (string) $element->type['result'] ) {
				$dimension_type_code = (string) $element->type;

				$message_type = (string) $element->type['msgtype'];

				if ( 'error' === $message_type ) {
					$messages = [
						'en-GB' => "Dimension type $dimension_type_code does not exist.",
						'nl-NL' => "Dimensietype $dimension_type_code bestaat niet.",
					];

					$message = (string) $element->type['msg'];

					if ( \in_array( $message, $messages, true ) ) {
						throw new DimensionTypeNotFoundException(
							(string) $element->office,
							(string) $element->type
						);
					}
				}
			}

			if ( '0' === (string) $element->code['result'] ) {
				$dimension_code = (string) $element->code;

				$message_type = (string) $element->code['msgtype'];

				if ( 'error' === $message_type ) {
					$messages = [
						'en-GB' => "Dimension $dimension_code does not exist.",
						'nl-NL' => "Dimensie $dimension_code bestaat niet.",
					];

					$message = (string) $element->code['msg'];

					if ( \in_array( $message, $messages, true ) ) {
						throw new DimensionNotFoundException(
							(string) $element->office,
							(string) $element->type,
							(string) $element->code
						);
					}
				}
			}

			throw new \Exception( 'Dimension result error: ' . $element->asXML() );
		}

		$office = $this->organisation->office( (string) $element->office );

		$dimension_type = $office->new_dimension_type( (string) $element->type );

		$dimension = $dimension_type->new_dimension( (string) $element->code );

		$dimension->set_office( $office );

		$dimension->set_status( (string) $element['status'] );
		$dimension->set_uid( (string) $element->uid );
		$dimension->set_name( (string) $element->name );
		$dimension->set_shortname( (string) $element->shortname );

		return $dimension;
	}
}
