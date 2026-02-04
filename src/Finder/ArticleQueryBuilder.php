<?php
/**
 * Article Query Builder
 *
 * @package Pronamic/WordPress/Twinfield/Finder
 */

namespace Pronamic\WordPress\Twinfield\Finder;

/**
 * Article Query Builder
 *
 * Fluent interface for querying Twinfield articles/items.
 *
 * @package Pronamic/WordPress/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class ArticleQueryBuilder extends FinderQueryBuilder {
	/**
	 * Constructor.
	 *
	 * @param Finder $finder The finder instance.
	 */
	public function __construct( Finder $finder ) {
		parent::__construct( $finder, FinderTypes::ART );
	}
}
