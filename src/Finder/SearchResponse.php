<?php
/**
 * Search response
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Finder;

use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Search response
 *
 * This class represents an Twinfield search response.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SearchResponse implements JsonSerializable {
	/**
	 * The Twinfield search result code.
	 *
	 * @var ArrayOfMessageOfErrorCodes
	 */
	private $search_result;

	/**
	 * The Twinfield search data.
	 *
	 * @var FinderData
	 */
	private $data;

	/**
	 * Construct search response.
	 * 
	 * @param ArrayOfMessageOfErrorCodes $search_result Result.
	 * @param FinderData                 $data          Data.
	 */
	public function __construct( $search_result, $data ) {
		$this->search_result = $search_result;
		$this->data          = $data;
	}

	/**
	 * Helper function to check if this response is successful.
	 *
	 * @return boolean
	 */
	public function is_successful() {
		$array = $this->search_result->get_array();

		return empty( $array );
	}

	/**
	 * Get the search response result code.
	 *
	 * @return string
	 */
	public function get_search_result() {
		return $this->search_result;
	}

	/**
	 * Get the search response data.
	 *
	 * @return FinderData
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'result' => $this->search_result,
			'data'   => $this->data,
		];
	}

	/**
	 * From Twinfield object.
	 * 
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		return new self(
			$data->get_property( 'SearchResult' ),
			FinderData::from_twinfield_object( $data->get_property( 'data' ) )
		);
	}
}
