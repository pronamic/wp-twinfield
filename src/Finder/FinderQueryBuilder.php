<?php
/**
 * Finder Query Builder
 *
 * @package Pronamic/WordPress/Twinfield/Finder
 */

namespace Pronamic\WordPress\Twinfield\Finder;

/**
 * Finder Query Builder
 *
 * Base class for building finder queries with a fluent interface.
 *
 * @package Pronamic/WordPress/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
abstract class FinderQueryBuilder {
	/**
	 * The finder instance.
	 *
	 * @var Finder
	 */
	protected $finder;

	/**
	 * The finder type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The search pattern.
	 *
	 * @var string
	 */
	protected $pattern = '*';

	/**
	 * The search field.
	 *
	 * @var int
	 */
	protected $field = 0;

	/**
	 * First row to return.
	 *
	 * @var int
	 */
	protected $first_row = 1;

	/**
	 * Maximum number of rows to return.
	 *
	 * @var int
	 */
	protected $max_rows = 100;

	/**
	 * Query options.
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Constructor.
	 *
	 * @param Finder $finder The finder instance.
	 * @param string $type   The finder type.
	 */
	public function __construct( Finder $finder, string $type ) {
		$this->finder = $finder;
		$this->type   = $type;
	}

	/**
	 * Set the search pattern.
	 *
	 * @param string $pattern Search pattern (may contain wildcards * and ?).
	 * @return static
	 */
	public function pattern( string $pattern ) {
		$this->pattern = $pattern;

		return $this;
	}

	/**
	 * Alias for pattern method.
	 *
	 * @param string $pattern Search pattern.
	 * @return static
	 */
	public function where( string $pattern ) {
		return $this->pattern( $pattern );
	}

	/**
	 * Set the search field.
	 *
	 * @param int $field Search field constant from SearchFields.
	 * @return static
	 */
	public function searchField( int $field ) {
		$this->field = $field;

		return $this;
	}

	/**
	 * Set the first row for pagination.
	 *
	 * @param int $first_row First row to return.
	 * @return static
	 */
	public function offset( int $first_row ) {
		$this->first_row = $first_row;

		return $this;
	}

	/**
	 * Set the maximum number of rows to return.
	 *
	 * @param int $max_rows Maximum number of rows.
	 * @return static
	 */
	public function limit( int $max_rows ) {
		$this->max_rows = $max_rows;

		return $this;
	}

	/**
	 * Set pagination parameters.
	 *
	 * @param int $page    Page number (1-based).
	 * @param int $per_page Items per page.
	 * @return static
	 */
	public function paginate( int $page = 1, int $per_page = 100 ) {
		$this->first_row = ( ( $page - 1 ) * $per_page ) + 1;
		$this->max_rows  = $per_page;

		return $this;
	}

	/**
	 * Set a custom option.
	 *
	 * @param string $key   Option key.
	 * @param string $value Option value.
	 * @return static
	 */
	public function option( string $key, string $value ) {
		$this->options[ $key ] = $value;

		return $this;
	}

	/**
	 * Set the office code.
	 *
	 * @param string $office_code Office code.
	 * @return static
	 */
	public function office( string $office_code ) {
		$this->options['office'] = $office_code;

		return $this;
	}

	/**
	 * Build the Search object.
	 *
	 * @return Search
	 */
	protected function build(): Search {
		return new Search(
			$this->type,
			$this->pattern,
			$this->field,
			$this->first_row,
			$this->max_rows,
			$this->options
		);
	}

	/**
	 * Execute the query and get the response.
	 *
	 * @return SearchResponse
	 */
	public function execute(): SearchResponse {
		$search = $this->build();

		return $this->finder->search( $search );
	}

	/**
	 * Execute the query and get the data.
	 *
	 * @return FinderData
	 * @throws \RuntimeException If there are error messages or no data in the response.
	 */
	public function get(): FinderData {
		$response = $this->execute();

		$response->throw_if_error();

		$data = $response->get_data();

		if ( null === $data ) {
			throw new \RuntimeException( 'No data received from Twinfield API.' );
		}

		return $data;
	}

	/**
	 * Execute the query and get the items.
	 *
	 * @return array
	 */
	public function items(): array {
		$data = $this->get();

		if ( null === $data ) {
			return [];
		}

		return $data->get_items() ?? [];
	}

	/**
	 * Execute the query and get the first item.
	 *
	 * @return array|null
	 */
	public function first(): ?array {
		$items = $this->limit( 1 )->items();

		return $items[0] ?? null;
	}

	/**
	 * Execute the query and count the results.
	 *
	 * @return int
	 */
	public function count(): int {
		$data = $this->get();

		if ( null === $data ) {
			return 0;
		}

		return $data->get_total_rows();
	}
}
