<?php
/**
 * Declarations service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Declarations;

use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;
use Pronamic\WordPress\Twinfield\Session;

/**
 * Declarations service
 *
 * This class connects to the Twinfield declarations Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DeclarationsService extends AbstractService {
	/**
	 * The Twinfield declarations WSDL URL.
	 *
	 * @var string
	 */
	const WSDL_FILE = '/webservices/declarations.asmx?wsdl';

	/**
	 * Constructs and initializes a declarations service object.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		parent::__construct( self::WSDL_FILE, $client );
	}

	/**
	 * Get all sumaries of the specified office code.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $office_code The code of the office for which the returns should be retrieved. Mandatory.
	 * @return array
	 */
	public function get_all_summaries( $office ) {
		$parameters = new \stdClass();

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.
		$parameters->companyCode = $office->get_code();

		$soap_client = $this->get_soap_client( $office );

		$result = $soap_client->GetAllSummaries( $parameters );

		if ( ! is_object( $result ) ) {
			throw new \Exception(
				\sprintf(
					'Unknow response from declarations webservice: %s',
					\print_r( $result, true )
				)
			);
		}

		if ( ! isset( $result->GetAllSummariesResult ) ) {
			throw new \Exception(
				\sprintf(
					'No summaries result from declarations webservice: %s',
					\print_r( $result, true )
				)
			);
		}

		if ( ! isset( $result->vatReturn, $result->vatReturn->DeclarationSummary ) ) {
			return array();
		}

		$organisation = $office->organisation;

		$summaries = \array_map(
			function( $item ) use ( $organisation ) {
				return DeclarationSummary::from_twinfield_object( $organisation, $item );
			},
			$this->force_array( $result->vatReturn->DeclarationSummary )
		);

		return $summaries;
	}

	/**
	 * Get payment reference for the specified document code and ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id   The document ID.
	 * @param string $document_code The document code.
	 * @return string
	 */
	public function get_payment_reference( $document_id, $document_code = null ) {
		$function = 'GetVatReturnPaymentReference';

		switch ( $document_code ) {
			case DocumentCodes::VATTURNOVER:
				$function = 'GetVatReturnPaymentReference';

				break;
			case DocumentCodes::TAXGROUP:
				$function = 'GetTaxGroupVatReturnPaymentReference';

				break;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.

		$parameters = new \stdClass();

		$parameters->documentId = $document_id;

		$response = $this->soap_client->__soapCall( $function, array( $parameters ) );

		if ( isset( $response->paymentReference ) ) {
			return $response->paymentReference;
		}

		// phpcs:enable
	}

	/**
	 * Get VAT return payment reference for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id The document ID.
	 * @return string
	 */
	public function get_vat_return_payment_reference( $document_id ) {
		return $this->get_payment_reference( $document_id, DocumentCodes::VATTURNOVER );

	}

	/**
	 * Get TAX group payment reference for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id The document ID.
	 * @return string
	 */
	public function get_tax_group_vat_return_payment_reference( $document_id ) {
		return $this->get_payment_reference( $document_id, DocumentCodes::TAXGROUP );

	}

	/**
	 * Get XBRL for the specified document code and ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id   The document ID.
	 * @param string $document_code The document code.
	 * @return string
	 */
	public function get_xbrl( $document_id, $document_code = null ) {
		$soap_client = $this->get_soap_client();

		$function = 'GetVatReturnAsXbrl';

		switch ( $document_code ) {
			case DocumentCodes::VATTURNOVER:
				$function = 'GetVatReturnAsXbrl';

				break;
			case DocumentCodes::VATICT:
				$function = 'GetIctReturnAsXbrl';

				break;
			case DocumentCodes::TAXGROUP:
				$function = 'GetTaxGroupVatReturnAsXbrl';

				break;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.

		$parameters = new \stdClass();

		$parameters->documentId          = $document_id;
		$parameters->isMessageIdRequired = true;

		$response = $soap_client->__soapCall( $function, array( $parameters ) );

		if ( isset( $response->vatReturn, $response->vatReturn->any ) ) {
			return $response->vatReturn->any;
		}

		// phpcs:enable
	}

	public function get_xbrl_by_summary( $summary ) {
		$this->set_office( $summary->company );

		return $this->get_xbrl( $summary->id, $summary->document_code );
	}

	public function get_xml( $document_id, $document_code = null ) {
		$function = 'GetVatReturnAsXml';

		switch ( $document_code ) {
			case DocumentCodes::VATTURNOVER:
				$function = 'GetVatReturnAsXml';

				break;
			case DocumentCodes::VATICT:
				$function = 'GetIctReturnAsXml';

				break;
			case DocumentCodes::TAXGROUP:
				$function = 'GetTaxGroupVatReturnAsXml';

				break;
		}

		$parameters = new \stdClass();

		$parameters->documentId = $document_id;

		$response = $this->soap_client->__soapCall( $function, array( $parameters ) );

		if ( isset( $response->vatReturn, $response->vatReturn->any ) ) {
			return $response->vatReturn->any;			
		}
	}

	/**
	 * Get VAT return XBRL for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id The document ID.
	 * @return string
	 */
	public function get_vat_return_as_xbrl( $document_id ) {
		return $this->get_xbrl( $document_id, DocumentCodes::VATTURNOVER );
	}

	/**
	 * Get ICT VAT return XBRL for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id The document ID.
	 * @return string
	 */
	public function get_ict_return_as_xbrl( $document_id ) {
		return $this->get_xbrl( $document_id, DocumentCodes::VATICT );
	}

	/**
	 * Get TAX group VAT return XBRL for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id The document ID.
	 * @return string
	 */
	public function get_tax_group_vat_return_as_xbrl( $document_id ) {
		return $this->get_xbrl( $document_id, DocumentCodes::TAXGROUP );
	}
}
