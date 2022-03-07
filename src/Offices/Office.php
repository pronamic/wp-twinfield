<?php
/**
 * Office
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use Pronamic\WordPress\Twinfield\CodeName;
use Pronamic\WordPress\Twinfield\Organisation\Organisation;
use Pronamic\WordPress\Twinfield\Accounting\TransactionType;
use Pronamic\WordPress\Twinfield\Accounting\DimensionType;

/**
 * Office
 *
 * This class represents an Twinfield office
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Office extends CodeName implements \JsonSerializable {
	/**
	 * Organisation.
	 *
	 * @var Organisation|null
	 */
	public $organisation;

	private $transaction_types = array();

	private $dimension_types = array();

	private $sales_invoice_types = array();

	public function new_transaction_type( $code ) {
		$transaction_type = new TransactionType( $this, $code );

		$this->transaction_types[ $code ] = $transaction_type;

		return $transaction_type;
	}

	public function new_dimension_type( $code ) {
		$dimension_type = new DimensionType( $this, $code );

		$this->dimension_types[] = $dimension_type;

		return $dimension_type;
	}

	public function sales_invoice_type( $code ) {
		$sales_invoice_type = new \Pronamic\WordPress\Twinfield\Accounting\SalesInvoiceType( $this, $code );

		$this->sales_invoice_types[] = $sales_invoice_type;

		return $sales_invoice_type;
	}

	/**
	 * From XML.
	 */
	public static function from_xml( $xml, $office ) {
		$simplexml = \simplexml_load_string( $xml );

		if ( false === $simplexml ) {
			throw new \Exception( 'Could not parse XML.' );
		}

		if ( 'office' !== $simplexml->getName() ) {
			throw new \Exception( 'Invalid element name.' );   
		}

		$result = \strval( $simplexml['result'] );

		if ( '1' !== $result ) {
			throw new \Exception( \strval( $simplexml['msg'] ) );
		}

		$office->set_name( \strval( $simplexml->name ) );
		$office->set_shortname( \strval( $simplexml->shortname ) );

		$user = $office->organisation->new_user( \strval( $simplexml->user ) );

		return $office;
	}

	public function jsonSerialize() {
		return (object) array(
			'code'      => $this->get_code(),
			'name'      => $this->get_name(),
			'shortname' => $this->get_shortname(),
		);
	}
}
