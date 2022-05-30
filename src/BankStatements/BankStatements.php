<?php
/**
 * Bank statements
 *
 * @package Pronamic\WordPress\Twinfield
 */

namespace Pronamic\WordPress\Twinfield\BankStatements;

use DateTimeInterface;
use DateTimeImmutable;
use IteratorAggregate;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Bank statements class
 */
class BankStatements implements IteratorAggregate, JsonSerializable {
	/**
	 * Statements.
	 * 
	 * @var BankStatement[]
	 */
	private $items = [];

	/**
	 * From Twinfield object.
	 * 
	 * @param object $object Object.
	 */
	public static function from_twinfield_object( $object ) {
		$data = ObjectAccess::from_object( $object );

		$bank_statements = new self();

		if ( $data->has_property( 'BankStatement' ) ) {
			foreach ( $data->get_array( 'BankStatement' ) as $bank_statement_object ) {
				$bank_statements->items[] = BankStatement::from_twinfield_object( $bank_statement_object );
			}
		}

		return $bank_statements;
	}

	/**
	 * Get iterator.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->items );
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return $this->items;
	}
}
