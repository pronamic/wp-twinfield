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
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Hierarchy Account
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
final class HierarchyAccount implements JsonSerializable, \Stringable {
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
	public function __toString(): string {
		return \sprintf(
			'%s - %s - %s',
			$this->type,
			$this->code,
			$this->balance_type
		);
	}

	/**
	 * Convert from Twinfield object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$account = new self(
			$data->get_property( 'Type' ),
			$data->get_property( 'Code' ),
			$data->get_property( 'BalanceType' )
		);

		return $account;
	}

	/**
	 * From JSON object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_json_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$account = new HierarchyAccount(
			$data->get_property( 'type' ),
			$data->get_property( 'code' ),
			$data->get_property( 'balance_type' )
		);

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
