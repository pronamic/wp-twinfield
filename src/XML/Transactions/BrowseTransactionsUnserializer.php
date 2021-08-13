<?php
/**
 * Browse transactions unserializer
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML/Articles
 */

namespace Pronamic\WordPress\Twinfield\XML\Transactions;

use Pronamic\WordPress\Twinfield\Currency;
use Pronamic\WordPress\Twinfield\VatCode;
use Pronamic\WordPress\Twinfield\Browse\Row;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Relations\Relation;
use Pronamic\WordPress\Twinfield\Transactions\Transaction;
use Pronamic\WordPress\Twinfield\Transactions\TransactionHeader;
use Pronamic\WordPress\Twinfield\Transactions\TransactionTypeCode;
use Pronamic\WordPress\Twinfield\Transactions\TransactionLine;
use Pronamic\WordPress\Twinfield\Transactions\TransactionLineDimension;
use Pronamic\WordPress\Twinfield\Transactions\TransactionLineKey;
use Pronamic\WordPress\Twinfield\Transactions\TransactionResponse;
use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\XML\Unserializer;
use Pronamic\WordPress\Twinfield\XML\DateUnserializer;
use Pronamic\WordPress\Twinfield\XML\DateTimeUnserializer;

/**
 * Browse transactions unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class BrowseTransactionsUnserializer extends Unserializer {
	/**
	 * Transactions.
	 *
	 * @var array
	 */
	private $transactions;

	/**
	 * Constructs and initializes a browse transaction unserializer.
	 */
	public function __construct( $browse_definition = null ) {
		$this->date_unserializer     = new DateUnserializer();
		$this->datetime_unserializer = new DateTimeUnserializer();
		$this->browse_definition     = $browse_definition;

		$this->transactions = array();
	}

	/**
	 * Get transactions.
	 *
	 * @return array
	 */
	public function get_transactions() {
		return $this->transactions;
	}

	/**
	 * Unserialize the specified XML to an article.
	 *
	 * @param \SimpleXMLElement $element The XML element to unserialize.
	 */
	public function unserialize( \SimpleXMLElement $element ) {
		if ( 'browse' !== $element->getName() ) {
			return;
		}

		$this->transactions = array();

		$lines = array();

		if ( $element->tr ) {
			foreach ( $element->tr as $tr ) {
				$row = new Row( $tr );

				$xml_key = $row->get_xml_key();

				$transaction_key = implode(
					'-',
					array(
						(string) $xml_key->office,
						(string) $xml_key->code,
						(string) $xml_key->number,
					)
				);

				if ( ! isset( $this->transactions[ $transaction_key ] ) ) {
					// Transaction.
					$transaction = new Transaction();
					$transaction->set_webservice_origin( 'browse' );
					$transaction->set_browse_definition( $this->browse_definition );

					$this->transactions[ $transaction_key ] = $transaction;

					// Header.
					$header = $transaction->get_header();

					// Office.
					$office_code = (string) $xml_key->office;

					if ( $row->has_field( 'fin.trs.head.office' ) ) {
						$office_code = $row->get_field( 'fin.trs.head.office' );
					}

					$office = new Office(
						$office_code,
						$row->get_field( 'fin.trs.head.officename' )
					);

					$header->set_office( $office );

					// Relation.
					$relation = new Relation(
						$row->get_field( 'fin.trs.head.relation' ),
						$row->get_field( 'fin.trs.head.relationname' )
					);

					$header->set_relation( $relation );

					// Currency.
					$currency = new Currency( $row->get_field( 'fin.trs.head.curcode' ) );

					$header->set_currency( $currency );

					// Code.
					$code = new TransactionTypeCode( $row->get_field( 'fin.trs.head.code' ) );

					$header->set_code( $code );

					// Other.
					$header->set_number( $row->get_field( 'fin.trs.head.number' ) );
					$header->set_status( $row->get_field( 'fin.trs.head.status' ) );
					$header->set_date( $this->date_unserializer->unserialize( $row->get_field( 'fin.trs.head.date' ) ) );

					if ( $row->has_field( 'fin.trs.head.inpdate' ) ) {
						$input_date = $this->datetime_unserializer->unserialize( $row->get_field( 'fin.trs.head.inpdate' ) );

						$header->set_input_date( $input_date );
					}

					$header->set_username( $row->get_field( 'fin.trs.head.username' ) );

					// Year/period.
					$year   = null;
					$period = null;

					if ( $row->has_field( 'fin.trs.head.yearperiod' ) ) {
						$year_period = $row->get_field( 'fin.trs.head.yearperiod' );

						$seperator_position = strpos( $year_period, '/' );

						if ( false !== $seperator_position ) {
							$year   = substr( $year_period, 0, $seperator_position );
							$period = substr( $year_period, $seperator_position + 1 );
						}
					}

					if ( $row->has_field( 'fin.trs.head.year' ) ) {
						$year = $row->get_field( 'fin.trs.head.year' );
					}

					if ( $row->has_field( 'fin.trs.head.period' ) ) {
						$period = $row->get_field( 'fin.trs.head.period' );
					}

					$header->set_year( $year );
					$header->set_period( $period );

					$header->set_origin( $row->get_field( 'fin.trs.head.origin' ) );
				}

				$transaction = $this->transactions[ $transaction_key ];

				$line = $transaction->new_line();
				$line->set_webservice_origin( 'browse' );
				$line->set_browse_definition( $this->browse_definition );

				$lines[] = $line;

				$key = new TransactionLineKey(
					(string) $xml_key->office,
					(string) $xml_key->code,
					(string) $xml_key->number,
					(string) $xml_key->line
				);

				$line->set_key( $key );
				$line->set_id( $key->get_line() );

				if ( $row->has_field( 'fin.trs.line.dim1' ) ) {
					$dimension_1 = new TransactionLineDimension(
						$row->get_field( 'fin.trs.line.dim1type' ),
						$row->get_field( 'fin.trs.line.dim1' ),
						$row->get_field( 'fin.trs.line.dim1name' )
					);
				
					$line->set_dimension_1( $dimension_1 );
				}

				if ( $row->has_field( 'fin.trs.line.dim2' ) ) {
					$dimension_2 = new TransactionLineDimension(
						$row->get_field( 'fin.trs.line.dim2type' ),
						$row->get_field( 'fin.trs.line.dim2' ),
						$row->get_field( 'fin.trs.line.dim2name' )
					);
				
					$line->set_dimension_2( $dimension_2 );
				}

				if ( $row->has_field( 'fin.trs.line.dim3' ) ) {
					$dimension_3 = new TransactionLineDimension(
						$row->get_field( 'fin.trs.line.dim3type' ),
						$row->get_field( 'fin.trs.line.dim3' ),
						$row->get_field( 'fin.trs.line.dim3name' )
					);
				
					$line->set_dimension_3( $dimension_3 );
				}

				$line->set_debit_credit( $row->get_field( 'fin.trs.line.debitcredit' ) );
				
				if ( $row->has_field( 'fin.trs.line.valuesigned' ) ) {
					$line->set_value_signed( filter_var( $row->get_field( 'fin.trs.line.valuesigned' ), FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE ) );
				}

				if ( $row->has_field( 'fin.trs.line.basevaluesigned' ) ) {
					$line->set_base_value_signed( filter_var( $row->get_field( 'fin.trs.line.basevaluesigned' ), FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE ) );
				}

				if ( $row->has_field( 'fin.trs.line.openbasevaluesigned' ) ) {
					$line->set_open_base_value_signed( filter_var( $row->get_field( 'fin.trs.line.openbasevaluesigned' ), FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE ) );
				}

				if ( $row->has_field( 'fin.trs.line.repvaluesigned' ) ) {
					$line->set_reporting_value_signed( filter_var( $row->get_field( 'fin.trs.line.repvaluesigned' ), FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE ) );
				}

				// VAT Code.
				$vat_code = $row->get_field( 'fin.trs.line.vatcode' );

				if ( ! empty( $vat_code ) ) {
					$line->set_vat_code( new VatCode( $vat_code ) );
				}

				$line->set_vat_base_value_signed( $row->get_field( 'fin.trs.line.vatbasevaluesigned' ) );
				$line->set_quantity( $row->get_field( 'fin.trs.line.quantity' ) );
				$line->set_cheque_number( $row->get_field( 'fin.trs.line.chequenumber' ) );
				$line->set_description( $row->get_field( 'fin.trs.line.description' ) );
				$line->set_invoice_number( $row->get_field( 'fin.trs.line.invnumber' ) );
				$line->set_free_text_1( $row->get_field( 'fin.trs.line.freetext1' ) );
				$line->set_free_text_2( $row->get_field( 'fin.trs.line.freetext2' ) );
				$line->set_free_text_3( $row->get_field( 'fin.trs.line.freetext3' ) );
				$line->set_due_date( $row->get_field( 'fin.trs.line.datedue' ) );
				$line->set_match_status( $row->get_field( 'fin.trs.line.matchstatus' ) );
				$line->set_match_number( $row->get_field( 'fin.trs.line.matchnumber' ) );

				if ( $row->has_field( 'fin.trs.line.matchdate' ) ) {
					$line->set_match_date( $this->date_unserializer->unserialize( $row->get_field( 'fin.trs.line.matchdate' ) ) );
				}
			}
		}

		return $lines;
	}
}
