<?php
/**
 * Articles finder
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 * @subpackage Pronamic/WP/Twinfield/Articles
 */

namespace Pronamic\WordPress\Twinfield\Articles;

use Pronamic\WordPress\Twinfield\Finder\Finder;
use Pronamic\WordPress\Twinfield\Finder\FinderTypes;
use Pronamic\WordPress\Twinfield\Finder\Search;
use Pronamic\WordPress\Twinfield\Finder\SearchFields;

/**
 * Articles Finder
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ArticlesFinder {
	/**
	 * The finder wich is used to connect with Twinfield.
	 *
	 * @var Finder
	 */
	private $finder;

	/**
	 * Constructs and initializes an custom finder object.
	 *
	 * @param Finder $finder The finder.
	 */
	public function __construct( Finder $finder ) {
		$this->finder = $finder;
	}

	/**
	 * Find articles.
	 *
	 * @param string $pattern   The pattern.
	 * @param string $field     The field.
	 * @param int    $first_row The first row.
	 * @param int    $max_rows  The max rows.
	 * @param array  $options   The options.
	 * @return array
	 */
	public function get_articles( $pattern, $field, $first_row, $max_rows, $options = [] ) {
		$articles = [];

		// Request.
		$search = new Search(
			FinderTypes::ART,
			$pattern,
			$field,
			$first_row,
			$max_rows,
			$options
		);

		$response = $this->finder->search( $search );

		// Parse.
		if ( $response ) {
			if ( $response->is_successful() ) {
				$data = $response->get_data();

				$items = $data->get_items();

				if ( ! is_null( $items ) ) {
					foreach ( $items as $item ) {
						$article = new ArticleFinderResult();
						$article->set_code( $item[0] );
						$article->set_name( $item[1] );

						$articles[] = $article;
					}
				}
			}
		}

		return $articles;
	}
}
