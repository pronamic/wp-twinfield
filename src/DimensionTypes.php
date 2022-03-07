<?php
/**
 * Dimension types
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Dimension types
 *
 * This class contains constants for different Twinfield dimension types.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DimensionTypes {
	/**
	 * Balancesheet
	 *
	 * @var string
	 */
	public const BAS = 'BAS';

	/**
	 * Profit and Loss
	 *
	 * @var string
	 */
	public const PNL = 'PNL';

	/**
	 * Accounts Payable
	 *
	 * @var string
	 */
	public const CRD = 'CRD';

	/**
	 * Accounts Receivable
	 *
	 * @var string
	 */
	public const DEB = 'DEB';

	/**
	 * Cost centers
	 *
	 * @var string
	 */
	public const KPL = 'KPL';

	/**
	 * Assets
	 *
	 * @var string
	 */
	public const AST = 'AST';

	/**
	 * Projects
	 *
	 * @var string
	 */
	public const PRJ = 'PRJ';

	/**
	 * Activities
	 *
	 * @var string
	 */
	public const ACT = 'ACT';
}
