<?php
/**
 * Dimension
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Dimensions;

use JsonSerializable;
use Pronamic\WordPress\Twinfield\Traits\CodeTrait;
use Pronamic\WordPress\Twinfield\Traits\NameTrait;
use Pronamic\WordPress\Twinfield\Traits\ShortnameTrait;
use Pronamic\WordPress\Twinfield\Traits\StatusTrait;
use Pronamic\WordPress\Twinfield\Traits\ModifiedTrait;
use Pronamic\WordPress\Twinfield\Traits\UniqueIdentificationTrait;
use Pronamic\WordPress\Twinfield\XML\Dimensions\DimensionUnserializer;

/**
 * Dimension
 *
 * This class represents an Twinfield dimension
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Dimension implements JsonSerializable {
	use CodeTrait;

	use NameTrait;

	use ShortnameTrait;

	use StatusTrait;

	use ModifiedTrait;

	use UniqueIdentificationTrait;

	/**
	 * Type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Constructs and initializes a dimension.
	 *
	 * @param string $type      Type.
	 * @param string $code      Code.
	 */
	public function __construct( $type, $code ) {
		$this->set_type( $type );
		$this->set_code( $code );
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
	 * Set type.
	 *
	 * @param string $type The type.
	 */
	public function set_type( $type ) {
		$this->type = $type;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'type'      => $this->type,
			'code'      => $this->get_code(),
			'name'      => $this->get_name(),
			'shortname' => $this->get_shortname(),
			'uid'       => $this->get_uid(),
		];
	}

	public static function from_xml( $xml, $office ) {
		$unserializer = new DimensionUnserializer();

		$simplexml = \simplexml_load_string( $xml );

		return $unserializer->unserialize( $simplexml );
	}
}
