<?php
/**
 * Problem tag exception
 *
 * @package Pronamic/WordPress/Twinfield/XML
 */

namespace Pronamic\WordPress\Twinfield\XML;

use DOMNode;
use Throwable;

/**
 * Problem tag exception class
 */
class ProblemTagException extends \Exception {
	/**
	 * Problem tag.
	 * 
	 * @var DOMNode
	 */
	public $problem_tag;

	/**
	 * Construct problem tag exception.
	 * 
	 * @param DOMNode   $problem_tag Problem tag.
	 * @param int       $code        Code.
	 * @param Throwable $previous    Previous.
	 */
	public function __construct( DOMNode $problem_tag, $code = 0, ?Throwable $previous = null ) {
		$this->problem_tag = $problem_tag;

		$message = \sprintf(
			'Twinfield error: %s (Type: %s) in tag <%s>',
			$problem_tag->getAttribute( 'msg' ),
			$problem_tag->getAttribute( 'msgtype' ),
			$problem_tag->nodeName
		);

		parent::__construct( $message, $code, $previous );
	}
}
