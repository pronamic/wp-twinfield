<?php
/**
 * Dimension
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Accounting;

use Pornamic\WP\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\XML\Dimensions\DimensionUnserializer;
use DOMDocument;
use JsonSerializable;

/**
 * Dimension
 *
 * This class represents a Twinfield dimension.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Dimension implements JsonSerializable {
	private $office;

	private $type;

	private $code;

	private $uid;

	public function __construct( $office, DimensionType $type, $code ) {
		$this->office = $office;
		$this->type   = $type;
		$this->code   = $code;
	}

	public function get_code() {
		return $this->code;
	}

	public function set_uid( $uid ) {
		$this->uid = $uid;
	}

	public function set_name( $name ) {
		$this->name = $name;
	}

	public function set_shortname( $shortname ) {
		$this->shortname = $shortname;
	}

	public function jsonSerialize() {
		return array(
			'office'    => $this->office,
			'type'      => $this->type,
			'code'      => $this->code,
			'uid'       => $this->uid,
			'name'      => $this->name,
			'shortname' => $this->shortname,
		);
	}

	public static function from_xml( $xml, $office ) {
		$unserializer = new DimensionUnserializer();
		
		$simplexml = \simplexml_load_string( $xml );

		return $unserializer->unserialize( $simplexml );
	}
}
