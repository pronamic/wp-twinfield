<?php
/**
 * Payment methods.
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

/**
 * Payment methods
 *
 * This class contains constants for different Twinfield payment methods.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class PaymentMethods {
	/**
	 * Cash.
	 *
	 * @var string
	 */
	public const CASH = 'cash';

	/**
	 * Bank.
	 *
	 * @var string
	 */
	public const BANK = 'bank';

	/**
	 * Cheque.
	 *
	 * @var string
	 */
	public const CHEQUE = 'cheque';

	/**
	 * Cash on delivery.
	 *
	 * @var string
	 */
	public const CASH_ON_DELIVERY = 'cashondelivery';

	/**
	 * DA.
	 *
	 * @var string
	 */
	public const DA = 'da';
}
