<?php
/**
 * XML post errors.
 *
 * @link  https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Types/XmlWebServices#Parsing-results
 * @since 1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\XML;

use SimpleXMLElement;

/**
 * XML post errors.
 *
 * @since   1.0.0
 * @package Pronamic/WordPress/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class XmlPostErrors extends \Exception {
	/**
	 * SimpleXML element.
	 * 
	 * @var SimpleXMLElement
	 */
	private $simplexml;

	/**
	 * Construct XML post errors.
	 * 
	 * @param SimpleXMLElement $simplexml SimpleXML element.
	 */
	public function __construct( SimpleXMLElement $simplexml ) {
		parent::__construct( 'Problem occurs during the posting of XML.' );

		$this->simplexml = $simplexml;
	}

	/**
	 * Get SimpleXML element.
	 * 
	 * @return SimpleXMLElement
	 */
	public function get_simplexml() {
		return $this->simplexml;
	}

	/**
	 * Get problem elements.
	 * 
	 * @return SimpleXMLElement[]
	 * @throws \Exception Throws exception when XPath query fails.
	 */
	public function get_problem_elements() {
		$problem_elements = $this->simplexml->xpath( '//*[@result="0"]' );

		if ( ! \is_array( $problem_elements ) ) {
			throw new \Exception( 'Could not XPath query problem elements.' );
		}

		$errors = [];

		foreach ( $problem_elements as $problem_element ) {
			$errors[] = (object) [
				'message' => (string) $problem_element['msg'],
				'type'    => (string) $problem_element['msgtype'],
				'element' => $problem_element,
			];
		}

		return $errors;
	}
}
