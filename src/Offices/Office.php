<?php
/**
 * Office
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Offices;

use JsonSerializable;
use Pronamic\WordPress\Twinfield\CodeName;
use Pronamic\WordPress\Twinfield\Organisation\Organisation;
use Pronamic\WordPress\Twinfield\Accounting\TransactionType;
use Pronamic\WordPress\Twinfield\Dimensions\DimensionType;
use Pronamic\WordPress\Twinfield\Traits\CodeTrait;
use Pronamic\WordPress\Twinfield\Traits\NameTrait;
use Pronamic\WordPress\Twinfield\Traits\ShortnameTrait;
use Pronamic\WordPress\Twinfield\Traits\StatusTrait;
use Pronamic\WordPress\Twinfield\Traits\ModifiedTrait;
use Pronamic\WordPress\Twinfield\Traits\CreatedTrait;
use Pronamic\WordPress\Twinfield\Traits\TouchedTrait;
use Pronamic\WordPress\Twinfield\Traits\UserTrait;

/**
 * Office
 *
 * This class represents an Twinfield office
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Office implements JsonSerializable {
	/**
	 * Organisation.
	 *
	 * @var Organisation|null
	 */
	private $organisation;

	use CodeTrait;

	use NameTrait;

	use ShortnameTrait;

	use StatusTrait;

	use ModifiedTrait;

	use CreatedTrait;

	use TouchedTrait;

	use UserTrait;

	private $transaction_types = [];

	private $dimension_types = [];

	private $sales_invoice_types = [];

	public function __construct( $organisation, $code ) {
		$this->organisation = $organisation;

		$this->set_code( $code );
	}

	public function get_organisation() {
		return $this->organisation;
	}

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
		$sales_invoice_type = new \Pronamic\WordPress\Twinfield\SalesInvoices\SalesInvoiceType( $this, $code );

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

		$office->set_status( (string) $simplexml['status'] );
		$office->set_name( (string) $simplexml->name );
		$office->set_shortname( (string) $simplexml->shortname );
		$office->set_modified_at( \DateTimeImmutable::createFromFormat( 'YmdHis', (string) $simplexml->modified, new \DateTimeZone( 'UTC' ) ) );
		$office->set_created_at( \DateTimeImmutable::createFromFormat( 'YmdHis', (string) $simplexml->created, new \DateTimeZone( 'UTC' ) ) );
		$office->set_touched( (string) $simplexml->touched );
		$office->set_user( $office->get_organisation()->new_user( (string) $simplexml->user ) );

		return $office;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'status'      => $this->get_status(),
			'code'        => $this->get_code(),
			'name'        => $this->get_name(),
			'shortname'   => $this->get_shortname(),
			'modified_at' => null === $this->modified_at ? null : $this->modified_at->format( \DATE_ATOM ),
			'created_at'  => null === $this->created_at ? null : $this->created_at->format( \DATE_ATOM ),
		];
	}
}
