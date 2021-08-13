<?php
/**
 * Security
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML
 */

namespace Pronamic\WordPress\Twinfield\XML;

/**
 * Security
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield/XML
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Security {
	/**
	 * Filter the specified XML variable.
	 *
	 * @param mixed $variable The variable to filter.
	 * @param int   $filter One the PHP filter constants.
	 */
	public static function filter( $variable, $filter = FILTER_UNSAFE_RAW ) {
		$result = null;

		if ( $variable ) {
			$result = filter_var( (string) $variable, $filter );
		}

		return $result;
	}
}
