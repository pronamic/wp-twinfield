<?php
/**
 * Sales Invoice Type
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/SalesInvoices
 */

namespace Pronamic\WordPress\Twinfield\SalesInvoices;

use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\ProcessXmlString;
use Pronamic\WordPress\Twinfield\XMLProcessor;
use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\Offices\Office;

/**
 * Sales Invoice Type
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SalesInvoiceType {
	/**
	 * Construct sales invoice type.
	 *
	 * @param Office $office Office.
	 * @param string $code   Code.
	 */
	public function __construct( Office $office, $code ) {
		$this->office = $office;
		$this->code   = $code;
	}
}
