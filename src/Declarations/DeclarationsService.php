<?php
/**
 * Declarations service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WP/Twinfield
 */

namespace Pronamic\WP\Twinfield\Declarations;

use Pronamic\WP\Twinfield\AbstractClient;
use Pronamic\WP\Twinfield\Session;

/**
 * Declarations service
 *
 * This class connects to the Twinfield declarations Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WP/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DeclarationsService extends AbstractClient {
	/**
	 * The Twinfield declarations WSDL URL.
	 *
	 * @var string
	 */
	const WSDL_FILE = '/webservices/declarations.asmx?wsdl';

	/**
	 * Constructs and initializes a declarations service object.
	 *
	 * @param Session $session The Twinfield session.
	 */
	public function __construct( Session $session ) {
		parent::__construct( self::WSDL_FILE, $session );
	}

	/**
	 * Get all sumaries of the specified office code.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $office_code The code of the office for which the returns should be retrieved. Mandatory.
	 * @return array
	 */
	public function get_all_summaries( $office_code ) {
		$parameters = new \stdClass();
		$parameters->companyCode = $office_code;

		return $this->soap_client->GetAllSummaries( $parameters );
	}

	/**
	 * Get payment reference for the specified document code and ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id
	 * @param string $document_code
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

		$parameters = new \stdClass();
		$parameters->documentId = $document_id;

		$response = $this->soap_client->__soapCall( $function, array( $parameters ) );

		if ( isset( $response->paymentReference ) ) {
			return $response->paymentReference;
		}
	}

	/**
	 * Get VAT return payment reference for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id
	 * @return string
	 */
	public function get_vat_return_payment_reference( $document_id ) {
		return $this->get_payment_reference( $document_id, DocumentCodes::VATTURNOVER );

	}

	/**
	 * Get TAX group payment reference for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id
	 * @return string
	 */
	public function get_tax_group_vat_return_payment_reference( $document_id ) {
		return $this->get_payment_reference( $document_id, DocumentCodes::TAXGROUP );

	}

	/**
	 * Get XBRL for the specified document code and ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id
	 * @param string $document_code
	 * @return string
	 */
	public function get_xbrl( $document_id, $document_code = null ) {
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

		$parameters = new \stdClass();
		$parameters->documentId = $document_id;
		$parameters->isMessageIdRequired = true;

		$response = $this->soap_client->__soapCall( $function, array( $parameters ) );

		if ( isset( $response->vatReturn, $response->vatReturn->any ) ) {
			return $response->vatReturn->any;
		}
	}

	/**
	 * Get VAT return XBRL for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id
	 * @return string
	 */
	public function get_vat_return_as_xbrl( $document_id ) {
		return $this->get_xbrl( $document_id, DocumentCodes::VATTURNOVER );
	}

	/**
	 * Get ICT VAT return XBRL for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id
	 * @return string
	 */
	public function get_ict_return_as_xbrl( $document_id ) {
		return $this->get_xbrl( $document_id, DocumentCodes::VATICT );
	}

	/**
	 * Get TAX group VAT return XBRL for the specified document ID.
	 *
	 * @see https://c5.twinfield.de/webservices/documentation/#/ApiReference/Miscellaneous/Declaration
	 * @param string $document_id
	 * @return string
	 */
	public function get_tax_group_vat_return_as_xbrl( $document_id ) {
		return $this->get_xbrl( $document_id, DocumentCodes::TAXGROUP );
	}

	/**
	 * Test.
	 */
	public function test() {
		var_dump( $test->vatReturn );

		var_dump( $this->soap_client->__getFunctions() );
		var_dump( $this->soap_client->__getTypes() );
	}
}
