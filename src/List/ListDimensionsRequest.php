<?php
/**
 * List dimensions request.
 *
 * @since      1.0.0
 * @see        https://c3.twinfield.com/webservices/documentation/#/GettingStarted/WebServicesOverview#List-entities
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Session
 *
 * This class represents an Twinfield session.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ListDimensionsRequest extends ListRequest {
	/**
	 * Constructs and initialize an Twinfield read request.
	 *
	 * @param string $office         Specify from wich office to read.
	 * @param string $dimension_type Specify what type of data to read.
	 */
	public function __construct( $office, $dimension_type ) {
		parent::__construct( 
			[
				'type'          => ListEntities::DIMENSIONS,
				'office'        => $office, // @todo check if this is required.
				'dimensiontype' => $dimension_type, // @todo check if this is required.
			]
		);
	}
}
