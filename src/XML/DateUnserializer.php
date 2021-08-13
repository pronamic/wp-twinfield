<?php
/**
 * Date unserializer
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML
 */

namespace Pronamic\WordPress\Twinfield\XML;

/**
 * Sales invoices unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DateUnserializer extends Unserializer {
	/**
	 * Unserialize the specified XML to an article.
	 *
	 * @param \SimpleXMLElement|string $element The XML element to unserialize.
	 */
	public function unserialize( $element ) {
		$date = \DateTimeImmutable::createFromFormat( 'Ymd', Security::filter( $element ), new \DateTimeZone( 'UTC' ) );

		if ( false !== $date ) {
			$date->setTime( 0, 0 );

			return $date;
		}

		return null;
	}
}
