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
use Pronamic\WordPress\Twinfield\CodeName;
use Pronamic\WordPress\Twinfield\Traits\StatusTrait;
use Pronamic\WordPress\Twinfield\Traits\ModifiedTrait;
use Pronamic\WordPress\Twinfield\Traits\UniqueIdentificationTrait;
use Pronamic\WordPress\Twinfield\Traits\OfficeTrait;

/**
 * Dimension
 *
 * This class represents an Twinfield dimension
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Dimension extends CodeName implements JsonSerializable {
	use StatusTrait;

	use ModifiedTrait;

	use UniqueIdentificationTrait;

	use OfficeTrait;

	/**
	 * Type.
	 *
	 * @var DimensionType
	 */
	private $type;

	/**
	 * Constructs and initializes a dimension.
	 *
	 * @param DimensionType $type      Type.
	 * @param string        $code      Code.
	 */
	public function __construct( $type, $code ) {
		parent::__construct( $code );

		$this->set_type( $type );
	}

	/**
	 * Get type.
	 *
	 * @return DimensionType
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set type.
	 *
	 * @param DimensionType $type The type.
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
			'status'    => $this->get_status(),
		];
	}

	/**
	 * Create dimension from XML.
	 *
	 * @param string       $xml          XML.
	 * @param Organisation $organisation Organisation.
	 */
	public static function from_xml( $xml, $organisation ) {
		$unserializer = new DimensionUnserializer( $organisation );

		$simplexml = \simplexml_load_string( $xml );

		return $unserializer->unserialize( $simplexml );
	}
}
