<?php
/**
 * Search response
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Search response
 *
 * This class represents an Twinfield search response.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SearchResponse {
	/**
	 * The Twinfield search result code.
	 *
	 * @var ArrayOfMessageOfErrorCodes
	 */
	private $SearchResult; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.MemberNotSnakeCase -- Twinfield vaiable name.

	/**
	 * The Twinfield search data.
	 *
	 * @var FinderData
	 */
	private $data;

	/**
	 * Helper function to check if this response is successful.
	 *
	 * @return boolean
	 */
	public function is_successful() {
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.
		$array = $this->SearchResult->get_array();

		return empty( $array );
	}

	/**
	 * Get the search response result code.
	 *
	 * @return string
	 */
	public function get_search_result() {
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar -- Twinfield vaiable name.
		return $this->SearchResult;
	}

	/**
	 * Get the search response data.
	 *
	 * @return FinderData
	 */
	public function get_data() {
		return $this->data;
	}
}
