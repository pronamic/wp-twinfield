<?php
/**
 * Article unserializer
 *
 * @link       http://pear.php.net/package/XML_Serializer/docs
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/XML/Articles
 */

namespace Pronamic\WordPress\Twinfield\XML\Articles;

use Pronamic\WordPress\Twinfield\XML\Unserializer;
use Pronamic\WordPress\Twinfield\Articles\Article;
use Pronamic\WordPress\Twinfield\Articles\ArticleHeader;
use Pronamic\WordPress\Twinfield\Articles\ArticleLine;

/**
 * Article unserializer
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ArticleUnserializer extends Unserializer {
	/**
	 * Unserialize the specified XML to an article.
	 *
	 * @param \SimpleXMLElement $element the XML element to unserialize.
	 */
	public function unserialize( \SimpleXMLElement $element ) {
		if ( 'article' === $element->getName() && '0' !== (string) $element['result'] ) {
			$header = new ArticleHeader();

			$header->set_office( (string) $element->header->office );
			$header->set_code( (string) $element->header->code );
			$header->set_type( (string) $element->header->type );
			$header->set_name( (string) $element->header->name );
			$header->shortname = (string) $element->header->shortname;

			$lines = [];

			foreach ( $element->lines->line as $element_line ) {
				$line = new ArticleLine();

				$line->set_name( (string) $element_line->name );
				$line->shortname = (string) $element_line->shortname;

				$lines[] = $line;
			}

			$article = new Article( $header, $lines );

			return $article;
		}
	}
}
