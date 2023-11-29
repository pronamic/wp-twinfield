<?php
/**
 * Customer unserializer
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML/Articles
 */

namespace Pronamic\WordPress\Twinfield\Customers;

use Pronamic\WordPress\Twinfield\Country;
use Pronamic\WordPress\Twinfield\DimensionTypes;
use Pronamic\WordPress\Twinfield\EmailList;
use Pronamic\WordPress\Twinfield\Organisations\Organisation;
use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\XML\Unserializer;
use SimpleXMLElement;

/**
 * Sales invoices unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CustomerUnserializer extends Unserializer {
	/**
	 * Construct customer unserializer.
	 * 
	 * @param Organisation $organisation Organisation.
	 */
	public function __construct( Organisation $organisation ) {
		$this->organisation = $organisation;
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

		if ( DimensionTypes::DEB !== (string) $element->type ) {
			throw new \Exception(
				\sprintf(
					'Invalid dimension type: %s.',
					\esc_html( (string) $element->type )
				)
			);
		}

		$customer = new Customer( (string) $element->type, (string) $element->code );

		$customer->set_status( (string) $element['status'] );

		$customer->set_uid( (string) $element->uid );
		$customer->set_office( $this->organisation->office( (string) $element->office ) );
		$customer->set_name( (string) $element->name );
		$customer->set_shortname( (string) $element->shortname );
	
		$financials = $customer->get_financials();

		$financials->set_due_days( (int) $element->financials->duedays );
		$financials->set_ebilling( 'true' === (string) $element->financials->ebilling );
		$financials->set_ebillmail( (string) $element->financials->ebillmail );

		$credit_management = $customer->get_credit_management();

		$credit_management->set_send_reminder( (string) $element->creditmanagement->sendreminder );
		$credit_management->set_reminder_email( (string) $element->creditmanagement->reminderemail );

		if ( $element->addresses ) {
			foreach ( $element->addresses->address as $element_address ) {
				$address = $customer->new_address();

				$address->set_id( Security::filter( $element_address['id'], FILTER_VALIDATE_INT ) );
				$address->set_type( Security::filter( $element_address['type'] ) );
				$address->set_default( Security::filter( $element_address['default'], FILTER_VALIDATE_BOOLEAN ) );

				$address->set_name( Security::filter( $element_address->name ) );

				// Country.
				$country = new Country(
					Security::filter( $element_address->country ),
					Security::filter( $element_address->country['name'] ),
					Security::filter( $element_address->country['shortname'] )
				);

				$address->set_country( $country );

				$address->set_city( Security::filter( $element_address->city ) );
				$address->set_postcode( Security::filter( $element_address->postcode ) );
				$address->set_telephone( Security::filter( $element_address->telephone ) );
				$address->set_telefax( Security::filter( $element_address->telefax ) );
				$address->set_email( Security::filter( $element_address->email ) );
				$address->set_contact( Security::filter( $element_address->contact ) );
				$address->set_field_1( Security::filter( $element_address->field1 ) );
				$address->set_field_2( Security::filter( $element_address->field2 ) );
				$address->set_field_3( Security::filter( $element_address->field3 ) );
				$address->set_field_4( Security::filter( $element_address->field4 ) );
				$address->set_field_5( Security::filter( $element_address->field5 ) );
				$address->set_field_6( Security::filter( $element_address->field6 ) );
			}
		}

		return $customer;
	}
}
