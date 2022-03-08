<?php
/**
 * Hierarchy Account
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use JsonSerializable;

/**
 * Hierarchy Account
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class HierarchyAccount implements JsonSerializable {
	/**
	 * The dimension type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The dimension code.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * The balance type of the dimension.
	 *
	 * @var string
	 */
	private $balance_type;

	/**
	 * Construct hierarchy account.
	 *
	 * @param string $type         Type.
	 * @param string $code         Code.
	 * @param string $balance_type Balance type.
	 */
	public function __construct( $type, $code, $balance_type ) {
		$this->type         = $type;
		$this->code         = $code;
		$this->balance_type = $balance_type;
	}

	/**
	 * Get type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get code.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * String.
	 *
	 * @return string
	 */
	public function __toString() {
		return \sprintf(
			'%s - %s - %s',
			$this->type,
			$this->code,
			$this->balance_type
		);
	}

	/**
	 * Convert from object.
	 *
	 * @param object $object Object.
	 * @return Hierarchy
	 */
	public static function from_object( $object ) {
		$account = new HierarchyAccount( $object->Type, $object->Code, $object->BalanceType );

		return $account;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'type'         => $this->type,
			'code'         => $this->code,
			'balance_type' => $this->balance_type,
		];
	}
}
