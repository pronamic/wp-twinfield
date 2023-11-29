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
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;
use Pronamic\WordPress\Twinfield\Traits\OfficeTrait;

/**
 * Bank statements class
 */
class BankStatements implements IteratorAggregate, JsonSerializable {
	use OfficeTrait;

	/**
	 * Statements.
	 * 
	 * @var BankStatement[]
	 */
	private $items = [];

	/**
	 * Contruct bank statements.
	 * 
	 * @param Office $office Office.
	 */
	public function __construct( Office $office ) {
		$this->office = $office;
	}

	/**
	 * From Twinfield object.
	 * 
	 * @param Office $office Office.
	 * @param object $value  Object.
	 */
	public static function from_twinfield_object( $office, $value ) {
		$data = ObjectAccess::from_object( $value );

		$bank_statements = new self( $office );

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
