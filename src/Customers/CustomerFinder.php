<?php
/**
 * Customer Finder
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Customers
 */

namespace Pronamic\WordPress\Twinfield\Customers;

use Pronamic\WordPress\Twinfield\Finder\Finder;
use Pronamic\WordPress\Twinfield\Finder\FinderTypes;
use Pronamic\WordPress\Twinfield\Finder\Search;
use Pronamic\WordPress\Twinfield\Finder\SearchFields;

/**
 * Customer Finder
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CustomerFinder {
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
	 * Find customers.
	 *
	 * @param string $pattern   The pattern.
	 * @param string $field     The field.
	 * @param int    $first_row The first row.
	 * @param int    $max_rows  The max rows.
	 * @param array  $options   The options.
	 * @return array
	 */
	public function get_customers( $pattern, $field, $first_row, $max_rows, $options = [] ) {
		$customers = [];

		// Options.
		$options['dimtype'] = 'DEB';

		// Request.
		$search = new Search(
			FinderTypes::DIM,
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
						$customer = new CustomerFinderResult();
						$customer->set_code( $item[0] );
						$customer->set_name( $item[1] );

						if ( SearchFields::BANK_ACCOUNT_NUMBER === $field ) {
							$customer->set_bank_account_number( $item[2] );
						}

						$customers[] = $customer;
					}
				}
			}
		}

		return $customers;
	}
}
