<?php
/**
 * Dimension Type
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Dimensions;

use JsonSerializable;

/**
 * Dimension Type
 *
 * This class represents a Twinfield dimension.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DimensionType implements JsonSerializable {
	private $office;

	private $code;

	/**
	 * Level.
	 *
	 * The level of the dimension determines what data you can capture.
	 * At level 1, the balance sheet and profit and loss accounts are
	 * captured, at level 2 relations (accounts payable and accounts
	 * receivable) and cost centres and at level 3 you can register
	 * projects and assets.
	 *
	 * @var int|null
	 */
	private $level;

	private $dimensions = [];

	public function __construct( $office, $code ) {
		$this->office = $office;
		$this->code   = $code;
	}

	public function jsonSerialize() {
		return [
			'office' => $this->office,
			'code'   => $this->code,
		];
	}

	public function new_dimension( $code ) {
		$dimension = new Dimension( $this->code, $code );

		$this->dimensions[] = $dimension;

		return $dimension;
	}

	/**
	 * Get level.
	 *
	 * @return int|null
	 */
	public function get_level() {
		return $this->level;
	}

	/**
	 * Set level.
	 *
	 * @param int|null $level The level.
	 */
	public function set_level( $level ) {
		$this->level = $level;
	}
}
