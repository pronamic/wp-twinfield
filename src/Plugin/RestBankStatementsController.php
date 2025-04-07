<?php
/**
 * REST electronic bank statements controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use DateTimeImmutable;
use WP_REST_Request;
use WP_REST_Response;

/**
 * REST electronic bank statements controller ckass
 */
class RestBankStatementsController extends RestController {
	/**
	 * REST API initialize.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @return void
	 */
	public function rest_api_init() {
		$namespace = 'pronamic-twinfield/v1';

		\register_rest_route(
			$namespace,
			'bank-statements',
			[
				'methods'             => 'POST',
				'callback'            => $this->rest_api_create( ... ),
				'permission_callback' => fn() => true,
				'args'                => [
					'authorization'    => $this->get_authorization_schema(),
					'office_code'      => [
						'description' => \__( 'Twinfield office code.', 'pronamic' ),
						'type'        => 'string',
						'required'    => true,
					],
					'code'             => [
						'description' => \__( 'Code of the corresponding bank book. Either `account` or `iban` or `code` should be set.', 'pronamic-twinfield' ),
						'type'        => 'string',
						'required'    => false,
					],
					'date'             => [
						'description' => \__( 'Bank statement date. Set to the current date when left empty.', 'pronamic-twinfield' ),
						'type'        => 'string',
						'required'    => false,
					],
					'statement_number' => [
						'description' => \__( 'Number of the bank statement. When left empty, last available bank statement number increased by one.', 'pronamic-twinfield' ),
						'type'        => 'integer',
						'required'    => false,
					],
					'transactions'     => [
						'description' => \__( 'Contains the bank statement transactions.', 'pronamic-twinfield' ),
						'type'        => 'array',
						'items'       => [
							'type'       => 'object',
							'properties' => [
								'contra_iban'  => [
									'description' => \__( 'Contra account number in IBAN format. Either use `contraaccount` or `contraiban` or leave empty.', 'pronamic-twinfield' ),
									'type'        => 'string',
									'required'    => false,
								],
								'type'         => [
									'description' => \__( 'Contra account number in IBAN format. Either use `contraaccount` or `contraiban` or leave empty.', 'pronamic-twinfield' ),
									'type'        => 'string',
									'maxLength'   => 4,
									'required'    => true,
								],
								'reference'    => [
									'description' => \__( 'Reference for own use.', 'pronamic-twinfield' ),
									'type'        => 'string',
									'maxLength'   => 40,
									'required'    => true,
								],
								'debit_credit' => [
									'description' => \__( '`debit` = money is paid by bank, `credit` = money is received by bank', 'pronamic-twinfield' ),
									'type'        => 'string',
									'enum'        => [
										'debit',
										'credit',
									],
									'required'    => true,
								],
								'value'        => [
									'description' => \__( 'Amount of the transaction.', 'pronamic-twinfield' ),
									'type'        => 'number',
									'required'    => true,
								],
								'description'  => [
									'description' => \__( 'Description of the transaction.', 'pronamic-twinfield' ),
									'type'        => 'string',
									'maxLength'   => 500,
									'required'    => true,
								],
							],
						],
					],
				],
			]
		);
	}

	/**
	 * REST API process XML.
	 * 
	 * @param WP_REST_Request $request WordPress REST API request object.
	 * @return WP_REST_Response
	 */
	public function rest_api_create( WP_REST_Request $request ) {
		/**
		 * XML.
		 */
		$document = new \DOMDocument( '1.0', 'UTF-8' );

		$document->preserveWhiteSpace = false;
		$document->formatOutput       = true;

		$element_statement = $document->createElement( 'statement' );
		$element_statement->setAttribute( 'target', 'electronicstatements' );

		$document->appendChild( $element_statement );

		$element_statement->appendChild( $document->createElement( 'code', $request->get_param( 'code' ) ) );

		if ( $request->has_param( 'date' ) ) {
			$value = $request->get_param( 'date' );

			$date = DateTimeImmutable::createFromFormat( 'Y-m-d', $value );

			$element_statement->appendChild( $document->createElement( 'date', $date->format( 'Ymd' ) ) );
		}
		
		$element_statement->appendChild( $document->createElement( 'statementnumber', $request->get_param( 'statement_number' ) ) );

		$element_transactions = $document->createElement( 'transactions' );

		$element_statement->appendChild( $element_transactions );

		$transactions = $request->get_param( 'transactions' );

		foreach ( $transactions as $transaction ) {
			$element_transaction = $document->createElement( 'transaction' );

			$element_transactions->appendChild( $element_transaction );

			if ( array_key_exists( 'contra_iban', $transaction ) ) {
				$element_transaction->appendChild( $document->createElement( 'contraiban', $transaction['contra_iban'] ) );
			}

			$element_transaction->appendChild( $document->createElement( 'type', $transaction['type'] ) );
			$element_transaction->appendChild( $document->createElement( 'reference', $transaction['reference'] ) );
			$element_transaction->appendChild( $document->createElement( 'debitcredit', $transaction['debit_credit'] ) );
			$element_transaction->appendChild( $document->createElement( 'value', $transaction['value'] ) );

			$element_description = $document->createElement( 'description' );
			$element_description->appendChild( $document->createTextNode( $transaction['description'] ) );

			$element_transaction->appendChild( $element_description );
		}

		/**
		 * Client.
		 */
		$post = $this->handle_authorization( $request );

		$client = $this->plugin->get_client( $post );

		$organisation = $client->get_organisation();

		$xml_processor = $client->get_xml_processor();

		// Office.
		$office_code = $request->get_param( 'office_code' );

		if ( ! empty( $office_code ) ) {
			$office = $organisation->office( $office_code );

			$xml_processor->set_office( $office );
		}

		$xml = $document->saveXML();

		$response = $xml_processor->process_xml_string( $xml );

		/**
		 * Envelope.
		 * 
		 * @link https://developer.wordpress.org/rest-api/using-the-rest-api/global-parameters/#_envelope
		 * @link https://jsonapi.org/format/#document-top-level
		 */
		$rest_response = new WP_REST_Response(
			[
				'_embedded' => (object) [
					'request'  => (string) $xml,
					'response' => (string) $response,
				],
			]
		);

		return $rest_response;
	}
}
