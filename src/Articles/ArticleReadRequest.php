<?php
/**
 * Article read request
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Articles;

use Pronamic\WordPress\Twinfield\ReadRequest;

/**
 * Article read request
 *
 * This class represents an Twinfield article read request.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ArticleReadRequest extends ReadRequest {
	/**
	 * Constructs and initialize an Twinfield article read request.
	 *
	 * @param string $office  Specify from wich office to read.
	 * @param string $code    Specifcy which article code to read.
	 */
	public function __construct( $office, $code ) {
		parent::__construct(
			[
				'type'   => 'article',
				'office' => $office,
				'code'   => $code,
			]
		);
	}
}
