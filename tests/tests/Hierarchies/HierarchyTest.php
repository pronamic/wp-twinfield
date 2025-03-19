<?php
/**
 * Hierarchy test
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use PHPUnit\Framework\TestCase;

/**
 * Hierarchy test class
 */
final class HierarchyTest extends TestCase {
	/**
	 * Test JSON decode.
	 */
	public function test_json_decode() {
		$file = __DIR__ . '/../../json/hierarchies/twfrgs32.json';

		$this->assertFileExists( $file );

		$json = \file_get_contents( $file );

		$hierarchy = Hierarchy::from_json( $json );

		$this->assertInstanceOf( Hierarchy::class, $hierarchy );

		$this->assertJsonStringEqualsJsonString(
			$json,
			\json_encode( $hierarchy )
		);
	}
}
