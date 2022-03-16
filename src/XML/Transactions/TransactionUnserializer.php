<?php
/**
 * Transaction unserializer
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML/Articles
 */

namespace Pronamic\WordPress\Twinfield\XML\Transactions;

use DOMDocument;
use DOMNode;
use Pronamic\WordPress\Twinfield\CodeName;
use Pronamic\WordPress\Twinfield\Currency;
use Pronamic\WordPress\Twinfield\DestinationOffice;
use Pronamic\WordPress\Twinfield\Dimensions\Dimension;
use Pronamic\WordPress\Twinfield\Organisations\Organisation;
use Pronamic\WordPress\Twinfield\VatCode;
use Pronamic\WordPress\Twinfield\Offices\Office;
use Pronamic\WordPress\Twinfield\Transactions\Transaction;
use Pronamic\WordPress\Twinfield\Transactions\TransactionTypeCode;
use Pronamic\WordPress\Twinfield\Transactions\TransactionLineDimension;
use Pronamic\WordPress\Twinfield\Users\User;
use Pronamic\WordPress\Twinfield\XML\Security;
use Pronamic\WordPress\Twinfield\XML\Unserializer;
use Pronamic\WordPress\Twinfield\XML\DateUnserializer;
use Pronamic\WordPress\Twinfield\XML\DateTimeUnserializer;

/**
 * Transaction unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class TransactionUnserializer extends Unserializer {
	/**
	 * Constructs and initializes an sales invoice unserializer.
	 *
	 * @param Organisation $organisation Organisation.
	 */
	public function __construct( $organisation = null ) {
		$this->organisation = $organisation;

		$this->date_unserializer     = new DateUnserializer();
		$this->datetime_unserializer = new DateTimeUnserializer();
	}

	/**
	 * First.
	 *
	 * @link https://stackoverflow.com/questions/19555054/php-dom-how-to-get-child-elements-by-tag-name-in-an-elegant-manner
	 * @param DOMNode $node DOMNode.
	 * @param string  $name Name.
	 * @throws \Exception Throws exception when element could not be found.
	 */
	private function get_element( $node, $name ) {
		foreach ( $node->childNodes as $child ) {
			if ( $child->nodeName === $name ) {
				return $child;
			}
		}

		throw new \Exception(
			\sprintf(
				'Could not find element `%s` in `%s`.',
				$name,
				$node->nodeName
			)
		);
	}

	/**
	 * Unserialize the specified XML to an article.
	 *
	 * @param string $string The string to unserialize.
	 */
	public function unserialize_string( $string ) {
		$document = new DOMDocument();

		$document->loadXML( $string );

		$e_transaction = $this->get_element( $document, 'transaction' );

		$e_header = $this->get_element( $e_transaction, 'header' );

		$e_office = $this->get_element( $e_header, 'office' );

		$office = $this->organisation->office( $e_office->nodeValue );
		$office->set_name( $e_office->getAttribute( 'name' ) );
		$office->set_shortname( $e_office->getAttribute( 'shortname' ) );

		$e_code = $this->get_element( $e_header, 'code' );

		$transaction_type = $office->new_transaction_type( $e_code->nodeValue );
		$transaction_type->set_name( $e_code->getAttribute( 'name' ) );
		$transaction_type->set_shortname( $e_code->getAttribute( 'shortname' ) );

		$transaction = $transaction_type->new_transaction( $this->get_element( $e_header, 'number' )->nodeValue );

		$e_lines = $this->get_element( $e_transaction, 'lines' );

		foreach ( $e_lines->childNodes as $e_line ) {
			$line = $transaction->new_line();

			$line->set_id( $e_line->getAttribute( 'id' ) );
			$line->set_type( $e_line->getAttribute( 'type' ) );

			$line->set_base_value( $this->get_element( $e_line, 'basevalue' )->nodeValue );
			$line->set_open_base_value( $this->get_element( $e_line, 'basevalue' )->nodeValue );
		}

		return $transaction;
	}

	/**
	 * Unserialize the specified XML to an article.
	 *
	 * @param \SimpleXMLElement $element The XML element to unserialize.
	 */
	public function unserialize( \SimpleXMLElement $element ) {
		if ( 'transaction' === $element->getName() ) {
			$transaction = new Transaction();
			$transaction->set_webservice_origin( 'transactions' );

			$header = $transaction->get_header();

			// Location.
			$transaction->set_location( Security::filter( $element['location'] ) );

			// Header.
			if ( $element->header ) {
				// Office.
				$office = new Office(
					Security::filter( $element->header->office ),
					Security::filter( $element->header->office['name'] ),
					Security::filter( $element->header->office['shortname'] )
				);

				$header->set_office( $office );

				// Currency.
				$currency = new Currency(
					Security::filter( $element->header->currency ),
					Security::filter( $element->header->currency['name'] ),
					Security::filter( $element->header->currency['shortname'] )
				);

				$header->set_currency( $currency );

				// Transaction type code.
				$code = new TransactionTypeCode(
					Security::filter( $element->header->code ),
					Security::filter( $element->header->code['name'] ),
					Security::filter( $element->header->code['shortname'] )
				);

				$header->set_code( $code );

				// Number.
				$header->set_number( Security::filter( $element->header->number ) );

				// Regime.
				if ( $element->header->regime ) {               
					$header->set_regime( Security::filter( $element->header->regime ) );
				}

				// Date.
				$header->set_date( $this->date_unserializer->unserialize( $element->header->date ) );

				// Origin.
				if ( $element->header->origin ) {               
					$header->set_origin( Security::filter( $element->header->origin ) );
				}

				if ( $element->header->originreference ) {              
					$header->set_origin_reference( Security::filter( $element->header->originreference ) );
				}

				// Statement number.
				if ( $element->header->statementnumber ) {
					$header->set_statement_number( Security::filter( $element->header->statementnumber, FILTER_VALIDATE_INT ) );
				}

				// Start value.
				if ( $element->header->startvalue ) {
					$header->set_start_value( Security::filter( $element->header->startvalue, FILTER_VALIDATE_FLOAT ) );
				}

				// Close value.
				if ( $element->header->closevalue ) {
					$header->set_close_value( Security::filter( $element->header->closevalue, FILTER_VALIDATE_FLOAT ) );
				}

				// Input date.
				if ( $element->header->inputdate ) {
					$header->set_input_date( $this->datetime_unserializer->unserialize( $element->header->inputdate ) );
				}

				// Due date.
				if ( $element->header->duedate ) {
					$header->set_due_date( $this->date_unserializer->unserialize( $element->header->duedate ) );
				}

				// User.
				if ( $element->header->user ) {
					$user = new User(
						$this->organisation,
						Security::filter( $element->header->user )
					);

					$user->set_name( $element->header->user['name'] );
					$user->set_shortname( $element->header->user['shortname'] );

					$header->set_user( $user );
				}

				// Modification date.
				if ( $element->header->modificationdate ) {
					$header->set_modification_date( $this->datetime_unserializer->unserialize( $element->header->modificationdate ) );
				}

				// Year/period.
				$year   = null;
				$period = null;

				$year_period = Security::filter( $element->header->period );

				$seperator_position = strpos( $year_period, '/' );

				if ( false !== $seperator_position ) {
					$year   = substr( $year_period, 0, $seperator_position );
					$period = substr( $year_period, $seperator_position + 1 );
				}

				$header->set_year( $year );
				$header->set_period( $period );

				// Invoice number.
				$header->set_invoice_number( Security::filter( $element->header->invoicenumber ) );             

				// Free texts.
				$header->set_free_text_1( Security::filter( $element->header->freetext1 ) );
				$header->set_free_text_2( Security::filter( $element->header->freetext2 ) );
				$header->set_free_text_3( Security::filter( $element->header->freetext3 ) );
			}

			if ( $element->lines ) {
				foreach ( $element->lines->line as $element_line ) {
					$line = $transaction->new_line();
					$line->set_webservice_origin( 'transactions' );

					$line->set_id( Security::filter( $element_line['id'] ) );
					$line->set_type( Security::filter( $element_line['type'] ) );

					$dimensions_element = $element_line;

					/**
					 * The `localdim` element is not documented but is used when a 
					 * transaction line has another destination office. This can
					 * be used in case of tax groups.
					 */
					if ( $element_line->localdim ) {
						$dimensions_element = $element_line->localdim;
					}

					if ( $dimensions_element->dim1 ) {
						$dimension_1 = new Dimension(
							Security::filter( $dimensions_element->dim1['dimensiontype'] ),
							Security::filter( $dimensions_element->dim1 )
						);

						$dimension_1->set_name( $dimensions_element->dim1['name'] );
						$dimension_1->set_name( $dimensions_element->dim1['shortname'] );

						$line->set_dimension_1( $dimension_1 );
					}

					if ( $dimensions_element->dim2 ) {
						$dimension_2 = new Dimension(
							Security::filter( $dimensions_element->dim2['dimensiontype'] ),
							Security::filter( $dimensions_element->dim2 )
						);

						$dimension_2->set_name( $dimensions_element->dim2['name'] );
						$dimension_2->set_name( $dimensions_element->dim2['shortname'] );

						$line->set_dimension_2( $dimension_2 );
					}

					if ( $dimensions_element->dim3 ) {
						$dimension_3 = new Dimension(
							Security::filter( $dimensions_element->dim3['dimensiontype'] ),
							Security::filter( $dimensions_element->dim3 )
						);

						$dimension_3->set_name( $dimensions_element->dim3['name'] );
						$dimension_3->set_name( $dimensions_element->dim3['shortname'] );

						$line->set_dimension_3( $dimension_3 );
					}

					/**
					 * The `localdim` element is not documented but is used when a 
					 * transaction line has another destination office. This can
					 * be used in case of tax groups.
					 */
					if ( $element_line->destoffice ) {
						$destination_office = new DestinationOffice( Security::filter( $element_line->destoffice ) );

						if ( isset( $element_line->destoffice['dim1'] ) ) {
							$dimension_1 = new CodeName( Security::filter( $element_line->destoffice['dim1'] ) );

							$destination_office->set_dimension_1( $dimension_1 );
						}
					}

					$line->set_debit_credit( Security::filter( $element_line->debitcredit ) );

					if ( $element_line->basevalue ) {
						$line->set_base_value( Security::filter( $element_line->basevalue, FILTER_VALIDATE_FLOAT ) );
					}

					if ( $element_line->basevalueopen ) {
						$line->set_open_base_value( Security::filter( $element_line->basevalueopen, FILTER_VALIDATE_FLOAT ) );
					}

					if ( $element_line->value ) {
						$line->set_value( Security::filter( $element_line->value, FILTER_VALIDATE_FLOAT ) );
					}

					$line->set_description( Security::filter( $element_line->description ) );

					if ( $element_line->invoicenumber ) {
						$line->set_invoice_number( Security::filter( $element_line->invoicenumber ) );
					}

					if ( $element_line->matchstatus ) {
						$line->set_match_status( Security::filter( $element_line->matchstatus ) );
					}

					if ( $element_line->matchlevel ) {
						$line->set_match_level( Security::filter( $element_line->matchlevel ) );
					}

					if ( $element_line->matchdate ) {
						$line->set_match_date( $this->date_unserializer->unserialize( $element_line->matchdate ) );
					}

					if ( $element_line->matches ) {
						$line->matches = [];

						foreach ( $element_line->matches->set as $element_set ) {
							$match_set = new \Pronamic\WP\Twinfield\Transactions\MatchSet();

							$match_set->status      = Security::filter( $element_set['status'] );
							$match_set->match_date  = Security::filter( $element_set->matchdate );
							$match_set->match_value = Security::filter( $element_set->matchvalue, FILTER_VALIDATE_FLOAT );

							foreach ( $element_set->lines->line as $element_set_line ) {
								$match_set_line = new \Pronamic\WP\Twinfield\Transactions\MatchSetLine();

								$match_set_line->code        = Security::filter( $element_set_line->code );
								$match_set_line->number      = Security::filter( $element_set_line->number );
								$match_set_line->line        = Security::filter( $element_set_line->line );
								$match_set_line->method      = Security::filter( $element_set_line->method );
								$match_set_line->match_value = Security::filter( $element_set_line->matchvalue );

								$match_set->lines[] = $match_set_line;
							}

							$line->matches[] = $match_set;
						}
					}

					if ( $element_line->vattotal ) {
						$line->set_vat_total( Security::filter( $element_line->vattotal, FILTER_VALIDATE_FLOAT ) );
					}

					if ( $element_line->vatbasetotal ) {
						$line->set_vat_base_total( Security::filter( $element_line->vatbasetotal, FILTER_VALIDATE_FLOAT ) );
					}

					if ( $element_line->vatbasevalue ) {
						$line->set_vat_base_value( Security::filter( $element_line->vatbasevalue, FILTER_VALIDATE_FLOAT ) );
					}

					if ( $element_line->comment ) {
						$line->set_comment( Security::filter( $element_line->comment ) );
					}

					if ( $element_line->vatcode ) {
						$line->set_vat_code(
							new VatCode(
								Security::filter( $element_line->vatcode ),
								Security::filter( $element_line->vatcode['name'] ),
								Security::filter( $element_line->vatcode['shortname'] ),
								Security::filter( $element_line->vatcode['type'] )
							)
						);
					}

					if ( $element_line->freetext1 ) {
						$line->set_free_text_1( \strval( $element_line->freetext1 ) );
					}

					if ( $element_line->freetext2 ) {
						$line->set_free_text_2( \strval( $element_line->freetext2 ) );
					}

					if ( $element_line->freetext3 ) {
						$line->set_free_text_3( \strval( $element_line->freetext3 ) );
					}
				}
			}

			// Response.
			$result = Security::filter( $element['result'] );

			return $transaction;
		}
	}
}
