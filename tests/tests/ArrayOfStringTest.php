<?php
/**
 * Array of string test
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WP/Twinfield
 */

namespace Pronamic\WP\Twinfield;

use PHPUnit\Framework\TestCase;

/**
 * Array of array of string test
 *
 * @since      1.0.0
 * @package    Pronamic/WP/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ArrayOfStringTest extends TestCase {
	/**
	 * Test
	 */
	public function test() {
		global $credentials;

		$array_of_string = new ArrayOfString();
		$array_of_string->add( 'test' );

		$data = [];

		foreach ( $array_of_string as $string ) {
			$data[] = $string;
		}

		$this->assertCount( 1, $data );
	}
}
