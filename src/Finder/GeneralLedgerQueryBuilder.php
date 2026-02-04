<?php
/**
 * General Ledger Query Builder
 *
 * @package Pronamic/WordPress/Twinfield/Finder
 */

namespace Pronamic\WordPress\Twinfield\Finder;

/**
 * General Ledger Query Builder
 *
 * Fluent interface for querying Twinfield general ledger accounts.
 *
 * @package Pronamic/WordPress/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class GeneralLedgerQueryBuilder extends FinderQueryBuilder {
	/**
	 * Constructor.
	 *
	 * @param Finder $finder The finder instance.
	 */
	public function __construct( Finder $finder ) {
		parent::__construct( $finder, FinderTypes::GLA );
	}

	/**
	 * Include all types of general ledger accounts.
	 *
	 * @return static
	 */
	public function includeAllTypes() {
		$this->options['includealltype'] = '1';

		return $this;
	}
}
