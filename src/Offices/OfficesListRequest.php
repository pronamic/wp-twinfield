<?php
/**
 * Offices List Request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use Pronamic\WordPress\Twinfield\ListRequest;

/**
 * Offices List Request
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class OfficesListRequest extends ListRequest {
	public function __construct() {
		parent::__construct( [ 'type' => 'offices' ] );
	}
}
