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
use Pronamic\WordPress\Twinfield\Traits\CodeTrait;
use Pronamic\WordPress\Twinfield\Traits\OfficeTrait;

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
	use OfficeTrait;

	use CodeTrait;

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

	/**
	 * Dimensions.
	 *
	 * @var Dimension[]
	 */
	private $dimensions = [];

	/**
	 * Construct dimension type.
	 *
	 * @param Office $office Office.
	 * @param string $code   Code.
	 */
	public function __construct( $office, $code ) {
		$this->office = $office;
		$this->code   = $code;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'office' => $this->office,
			'code'   => $this->code,
		];
	}

	/**
	 * New dimension.
	 *
	 * @param string $code Code.
	 * @return Dimension
	 */
	public function new_dimension( $code ) {
		$dimension = new Dimension( $this, $code );

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
