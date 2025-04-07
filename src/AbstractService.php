<?php
/**
 * Abstract service
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Offices\Office;
use SoapClient;
use SoapHeader;

/**
 * Abstract service
 *
 * This class connects to the Twinfield Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
abstract class AbstractService {
	/**
	 * The WSDL file path.
	 *
	 * @var string
	 */
	private $wsdl_file;

	/**
	 * SOAP Client object.
	 *
	 * @var SoapClient
	 */
	private $soap_client;

	/**
	 * SOAP Header Authenication Name.
	 * 
	 * @var string
	 */
	protected $soap_header_authenication_name;

	/**
	 * Office.
	 * 
	 * @var Office|null
	 */
	private $office;

	/**
	 * Constructs and initializes a Twinfield client object.
	 *
	 * @param string $wsdl_file WSDL file path.
	 * @param Client $client    Client.
	 */
	public function __construct( $wsdl_file, Client $client ) {
		$this->wsdl_file = $wsdl_file;

		$this->client = $client;

		$this->soap_header_authenication_name = 'Header';

		$this->soap_client = $client->new_soap_client( $wsdl_file );
	}

	/**
	 * Set office.
	 * 
	 * @param Office|null $office Office.
	 */
	public function set_office( Office $office = null ) {
		$this->office = $office;
	}

	/**
	 * Get SOAP client.
	 *
	 * @param Office|null $office Office.
	 * @return SoapClient
	 */
	public function get_soap_client( Office $office = null ) {
		$authentication = $this->client->authenticate();

		$data = [
			'AccessToken' => $authentication->get_tokens()->get_access_token(),
		];

		$office ??= $this->office;

		if ( null !== $office ) {
			$data['CompanyCode'] = $office->get_code();
		}

		$soap_header = new SoapHeader(
			'http://www.twinfield.com/',
			$this->soap_header_authenication_name,
			$data
		);

		$this->soap_client->__setSoapHeaders( $soap_header );

		return $this->soap_client;
	}

	/**
	 * Force array.
	 * 
	 * @param mixed $value Value.
	 * @return array
	 */
	protected function force_array( $value ) {
		if ( \is_array( $value ) ) {
			return $value;
		}

		return [ $value ];
	}
}
