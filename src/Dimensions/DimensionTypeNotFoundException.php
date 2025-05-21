<?php
/**
 * Dimension type not found exception
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Dimensions;

/**
 * Dimension type not found exception class
 */
class DimensionTypeNotFoundException extends \Exception {
	/**
	 * Twinfield office code.
	 *
	 * @var string
	 */
	public string $office_code;

	/**
	 * Twinfield dimension type code.
	 *
	 * @var string
	 */
	public string $dimension_type_code;

	/**
	 * Constructor.
	 *
	 * @param string          $office_code         Office code.
	 * @param string          $dimension_type_code Dimension type code.
	 * @param string|null     $message             Optional custom message.
	 * @param int             $code                Exception code.
	 * @param \Throwable|null $previous            Previous exception.
	 */
	public function __construct(
		string $office_code,
		string $dimension_type_code,
		?string $message = null,
		int $exception_code = 0,
		?\Throwable $previous = null
	) {
		$this->office_code         = $office_code;
		$this->dimension_type_code = $dimension_type_code;

		$message ??= sprintf(
			'Dimension type "%s" not found in office "%s".',
			$dimension_type_code,
			$office_code
		);

		parent::__construct( $message, $exception_code, $previous );
    }
}
