<?php
/**
 * Finder
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Finder;

use Pronamic\WordPress\Twinfield\AbstractService;
use Pronamic\WordPress\Twinfield\Client;

/**
 * Finder
 *
 * This class connects to the Twinfield finder Webservices to search for Twinfield masters.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Finder extends AbstractService {
	/**
	 * The Twinfield finder WSDL URL.
	 *
	 * @var string
	 */
	public const WSDL_FILE = '/webservices/finder.asmx?wsdl';

	/**
	 * Constructs and initializes an finder object.
	 *
	 * @param Client $client Twinfield client object.
	 */
	public function __construct( Client $client ) {
		parent::__construct( self::WSDL_FILE, $client );
	}

	/**
	 * Send the specified search request to Twinfield.
	 *
	 * @param Search $search An Twinfield search object.
	 * @return SearchResponse
	 */
	public function search( Search $search ) {
		$soap_client = $this->get_soap_client();

		$response = $soap_client->Search( $search->to_twinfield_object() );

		return SearchResponse::from_twinfield_object( $response );
	}

	/**
	 * Create a query builder for offices.
	 *
	 * @return OfficeQueryBuilder
	 */
	public function offices(): OfficeQueryBuilder {
		return new OfficeQueryBuilder( $this );
	}

	/**
	 * Create a query builder for dimensions.
	 *
	 * @return DimensionQueryBuilder
	 */
	public function dimensions(): DimensionQueryBuilder {
		return new DimensionQueryBuilder( $this );
	}

	/**
	 * Create a query builder for articles/items.
	 *
	 * @return ArticleQueryBuilder
	 */
	public function articles(): ArticleQueryBuilder {
		return new ArticleQueryBuilder( $this );
	}

	/**
	 * Create a query builder for general ledger accounts.
	 *
	 * @return GeneralLedgerQueryBuilder
	 */
	public function generalLedger(): GeneralLedgerQueryBuilder {
		return new GeneralLedgerQueryBuilder( $this );
	}

	/**
	 * Create a generic query builder for any finder type.
	 *
	 * @param string $type Finder type constant from FinderTypes.
	 * @return FinderQueryBuilder
	 */
	public function query( string $type ): FinderQueryBuilder {
		return new class( $this, $type ) extends FinderQueryBuilder {};
	}
}
