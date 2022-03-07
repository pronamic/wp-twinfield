<?php
/**
 * Browse read request XML
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\XML\Browse;

use Pronamic\WordPress\Twinfield\XML\ReadRequestSerializer;
use Pronamic\WordPress\Twinfield\Browse\BrowseReadRequest;

/**
 * Browse read request XML
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class BrowseReadRequestSerializer extends ReadRequestSerializer {
	/**
	 * Constructs and initialize a article read request XML object.
	 *
	 * @param BrowseReadRequest $request the browse read request to serialize.
	 */
	public function __construct( BrowseReadRequest $request ) {
		parent::__construct();

		$root = $this->document->createElement( 'read' );

		$this->document->appendChild( $root );

		$elements = [
			'type'   => $request->get_type(),
			'office' => $request->get_office(),
			'code'   => $request->get_code(),
		];

		foreach ( $elements as $name => $value ) {
			$element = $this->document->createElement( $name );
			$element->appendChild( new \DOMText( $value ) );

			$root->appendChild( $element );
		}
	}
}
