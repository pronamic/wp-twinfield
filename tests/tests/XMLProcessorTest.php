<?php
/**
 * XML processor test
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WP/Twinfield
 */

namespace Pronamic\WP\Twinfield;

use PHPUnit\Framework\TestCase;
use Pronamic\WP\Twinfield\Authentication\WebServicesAuthenticationStrategy;
use Pronamic\WP\Twinfield\Articles\ArticleReadRequest;
use Pronamic\WP\Twinfield\XML\Articles\ArticleReadRequestSerializer;
use Pronamic\WP\Twinfield\XML\Articles\ArticleUnserializer;
use Pronamic\WP\Twinfield\Customers\CustomerReadRequest;
use Pronamic\WP\Twinfield\XML\Customers\CustomerReadRequestSerializer;

/**
 * XML processor test
 *
 * This class will test the Twinfield XML processor features.
 *
 * @since      1.0.0
 * @package    Pronamic/WP/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class XMLProcessorTest extends TestCase {
	/**
	 * Test processor
	 *
	 * @param string $xml An XML message to process.
	 * @dataProvider provider
	 */
	public function test_processor( $xml ) {
		global $credentials;

		$authentication_strategy = new WebServicesAuthenticationStrategy( $credentials );

		$client = new Client( $authentication_strategy );

		$client->login();

		// Test XML processor.
		$xml_processor = $client->get_xml_processor();

		$response = $xml_processor->process_xml_string( new ProcessXmlString( $xml ) );

		$this->assertInstanceOf( __NAMESPACE__ . '\ProcessXmlStringResponse', $response );
		$this->assertInternalType( 'string', $response->get_result() );
	}

	/**
	 * Provider function for the `test_processor` function.
	 *
	 * @return array an array with test data.
	 */
	public function provider() {
		$office_code   = getenv( 'TWINFIELD_OFFICE_CODE' );
		$article_code  = getenv( 'TWINFIELD_ARTICLE_CODE' );
		$customer_code = getenv( 'TWINFIELD_CUSTOMER_CODE' );

		return [
			[ new ArticleReadRequestSerializer( new ArticleReadRequest( $office_code, $article_code ) ) ],
			[ new CustomerReadRequestSerializer( new CustomerReadRequest( $office_code, $customer_code ) ) ],
		];
	}
}
