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
use Pronamic\WordPress\Twinfield\ArrayOfMessageOfErrorCodes;
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
	 * @var ArrayOfMessageOfErrorCodes|null
	 */
	private $search_result;

	/**
	 * The Twinfield search data.
	 *
	 * @var FinderData|null
	 */
	private $data;

	/**
	 * Construct search response.
	 *
	 * @param ArrayOfMessageOfErrorCodes|null $search_result Result.
	 * @param FinderData|null                 $data          Data.
	 */
	public function __construct( ?ArrayOfMessageOfErrorCodes $search_result, ?FinderData $data ) {
		$this->search_result = $search_result;
		$this->data          = $data;
	}

	/**
	 * Helper function to check if this response is successful.
	 *
	 * @return boolean
	 */
	public function is_successful() {
		if ( null === $this->search_result ) {
			return true;
		}

		return $this->search_result->is_empty();
	}

	/**
	 * Get the search response result code.
	 *
	 * @return ArrayOfMessageOfErrorCodes|null
	 */
	public function get_search_result() {
		return $this->search_result;
	}

	/**
	 * Get the search response data.
	 *
	 * @return FinderData|null
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Throw exception if there are errors in the search result.
	 *
	 * @return void
	 * @throws \RuntimeException If there are error messages.
	 */
	public function throw_if_error() {
		if ( null !== $this->search_result ) {
			$this->search_result->throw_if_error();
		}
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

		$search_result = null;
		if ( $data->has_property( 'SearchResult' ) ) {
			$search_result = ArrayOfMessageOfErrorCodes::from_twinfield_object( $data->get_property( 'SearchResult' ) );
		}

		$finder_data = null;
		if ( $data->has_property( 'data' ) ) {
			$finder_data = FinderData::from_twinfield_object( $data->get_property( 'data' ) );
		}

		return new self( $search_result, $finder_data );
	}
}
