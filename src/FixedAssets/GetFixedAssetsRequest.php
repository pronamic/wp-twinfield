<?php
/**
 * Get fixed assets request
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

/**
 * Get fixed assets request class
 */
class GetFixedAssetsRequest {
	/**
	 * Organisation ID.
	 *
	 * @var string
	 */
	public readonly string $organisation_id;

	/**
	 * Company ID.
	 *
	 * @var string
	 */
	public readonly string $company_id;

	/**
	 * Offset.
	 *
	 * @var int|null
	 */
	private ?int $offset = null;

	/**
	 * Limit.
	 *
	 * @var int|null
	 */
	private ?int $limit = null;

	/**
	 * Order by.
	 *
	 * @var string|null
	 */
	private ?string $order_by = null;

	/**
	 * Ascending.
	 *
	 * @var bool|null
	 */
	private ?bool $asc = null;

	/**
	 * Search pattern.
	 *
	 * @var string|null
	 */
	private ?string $search_pattern = null;

	/**
	 * Status.
	 *
	 * @var string|null
	 */
	private ?string $status = null;

	/**
	 * Regime.
	 *
	 * @var string|null
	 */
	private ?string $regime = null;

	/**
	 * Tax reason.
	 *
	 * @var string|null
	 */
	private ?string $tax_reason = null;

	/**
	 * Location ID.
	 *
	 * @var string|null
	 */
	private ?string $location_id = null;

	/**
	 * Group ID.
	 *
	 * @var string|null
	 */
	private ?string $group_id = null;

	/**
	 * Class ID.
	 *
	 * @var string|null
	 */
	private ?string $class_id = null;

	/**
	 * Type ID.
	 *
	 * @var string|null
	 */
	private ?string $type_id = null;

	/**
	 * Time related filters year.
	 *
	 * @var int|null
	 */
	private ?int $time_related_filters_year = null;

	/**
	 * Time related filters period number.
	 *
	 * @var int|null
	 */
	private ?int $time_related_filters_period_number = null;

	/**
	 * First use from.
	 *
	 * @var \DateTimeInterface|null
	 */
	private ?\DateTimeInterface $first_use_from = null;

	/**
	 * First use to.
	 *
	 * @var \DateTimeInterface|null
	 */
	private ?\DateTimeInterface $first_use_to = null;

	/**
	 * Investment from.
	 *
	 * @var \DateTimeInterface|null
	 */
	private ?\DateTimeInterface $investment_from = null;

	/**
	 * Investment to.
	 *
	 * @var \DateTimeInterface|null
	 */
	private ?\DateTimeInterface $investment_to = null;

	/**
	 * Amount from.
	 *
	 * @var float|null
	 */
	private ?float $amount_from = null;

	/**
	 * Amount to.
	 *
	 * @var float|null
	 */
	private ?float $amount_to = null;

	/**
	 * Fields.
	 *
	 * @var string|null
	 */
	private ?string $fields = null;

	/**
	 * Construct get fixed assets request.
	 *
	 * @param string $organisation_id Organisation ID.
	 * @param string $company_id      Company ID.
	 */
	public function __construct( string $organisation_id, string $company_id ) {
		$this->organisation_id = $organisation_id;
		$this->company_id      = $company_id;
	}

	/**
	 * Set offset.
	 *
	 * @param int $offset Offset.
	 * @return self
	 */
	public function offset( int $offset ): self {
		$this->offset = $offset;

		return $this;
	}

	/**
	 * Set limit.
	 *
	 * @param int $limit Limit.
	 * @return self
	 */
	public function limit( int $limit ): self {
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Set order by.
	 *
	 * @param string $order_by Order by.
	 * @return self
	 */
	public function orderBy( string $order_by ): self {
		$this->order_by = $order_by;

		return $this;
	}

	/**
	 * Set ascending.
	 *
	 * @param bool $asc Ascending.
	 * @return self
	 */
	public function asc( bool $asc = true ): self {
		$this->asc = $asc;

		return $this;
	}

	/**
	 * Set descending.
	 *
	 * @return self
	 */
	public function desc(): self {
		$this->asc = false;

		return $this;
	}

	/**
	 * Set search pattern.
	 *
	 * @param string $search_pattern Search pattern.
	 * @return self
	 */
	public function pattern( string $search_pattern ): self {
		$this->search_pattern = $search_pattern;

		return $this;
	}

	/**
	 * Set status.
	 *
	 * @param string $status Status.
	 * @return self
	 */
	public function status( string $status ): self {
		$this->status = $status;

		return $this;
	}

	/**
	 * Set regime.
	 *
	 * @param string $regime Regime.
	 * @return self
	 */
	public function regime( string $regime ): self {
		$this->regime = $regime;

		return $this;
	}

	/**
	 * Set tax reason.
	 *
	 * @param string $tax_reason Tax reason.
	 * @return self
	 */
	public function taxReason( string $tax_reason ): self {
		$this->tax_reason = $tax_reason;

		return $this;
	}

	/**
	 * Set location ID.
	 *
	 * @param string $location_id Location ID.
	 * @return self
	 */
	public function locationId( string $location_id ): self {
		$this->location_id = $location_id;

		return $this;
	}

	/**
	 * Set group ID.
	 *
	 * @param string $group_id Group ID.
	 * @return self
	 */
	public function groupId( string $group_id ): self {
		$this->group_id = $group_id;

		return $this;
	}

	/**
	 * Set class ID.
	 *
	 * @param string $class_id Class ID.
	 * @return self
	 */
	public function classId( string $class_id ): self {
		$this->class_id = $class_id;

		return $this;
	}

	/**
	 * Set type ID.
	 *
	 * @param string $type_id Type ID.
	 * @return self
	 */
	public function typeId( string $type_id ): self {
		$this->type_id = $type_id;

		return $this;
	}

	/**
	 * Set time related filters year.
	 *
	 * @param int $year Year.
	 * @return self
	 */
	public function year( int $year ): self {
		$this->time_related_filters_year = $year;

		return $this;
	}

	/**
	 * Set time related filters period number.
	 *
	 * @param int $period_number Period number.
	 * @return self
	 */
	public function period( int $period_number ): self {
		$this->time_related_filters_period_number = $period_number;

		return $this;
	}

	/**
	 * Set first use from.
	 *
	 * @param \DateTimeInterface $first_use_from First use from.
	 * @return self
	 */
	public function firstUseFrom( \DateTimeInterface $first_use_from ): self {
		$this->first_use_from = $first_use_from;

		return $this;
	}

	/**
	 * Set first use to.
	 *
	 * @param \DateTimeInterface $first_use_to First use to.
	 * @return self
	 */
	public function firstUseTo( \DateTimeInterface $first_use_to ): self {
		$this->first_use_to = $first_use_to;

		return $this;
	}

	/**
	 * Set investment from.
	 *
	 * @param \DateTimeInterface $investment_from Investment from.
	 * @return self
	 */
	public function investmentFrom( \DateTimeInterface $investment_from ): self {
		$this->investment_from = $investment_from;

		return $this;
	}

	/**
	 * Set investment to.
	 *
	 * @param \DateTimeInterface $investment_to Investment to.
	 * @return self
	 */
	public function investmentTo( \DateTimeInterface $investment_to ): self {
		$this->investment_to = $investment_to;

		return $this;
	}

	/**
	 * Set amount from.
	 *
	 * @param float $amount_from Amount from.
	 * @return self
	 */
	public function amountFrom( float $amount_from ): self {
		$this->amount_from = $amount_from;

		return $this;
	}

	/**
	 * Set amount to.
	 *
	 * @param float $amount_to Amount to.
	 * @return self
	 */
	public function amountTo( float $amount_to ): self {
		$this->amount_to = $amount_to;

		return $this;
	}

	/**
	 * Set fields.
	 *
	 * @param string $fields Fields.
	 * @return self
	 */
	public function fields( string $fields ): self {
		$this->fields = $fields;

		return $this;
	}

	/**
	 * Build query string.
	 *
	 * @return string
	 */
	public function build(): string {
		$params = [];

		if ( null !== $this->offset ) {
			$params['offset'] = $this->offset;
		}

		if ( null !== $this->limit ) {
			$params['limit'] = $this->limit;
		}

		if ( null !== $this->order_by ) {
			$params['orderBy'] = $this->order_by;
		}

		if ( null !== $this->asc ) {
			$params['asc'] = $this->asc ? 'true' : 'false';
		}

		if ( null !== $this->search_pattern ) {
			$params['searchPattern'] = $this->search_pattern;
		}

		if ( null !== $this->status ) {
			$params['status'] = $this->status;
		}

		if ( null !== $this->regime ) {
			$params['regime'] = $this->regime;
		}

		if ( null !== $this->tax_reason ) {
			$params['taxReason'] = $this->tax_reason;
		}

		if ( null !== $this->location_id ) {
			$params['locationId'] = $this->location_id;
		}

		if ( null !== $this->group_id ) {
			$params['groupId'] = $this->group_id;
		}

		if ( null !== $this->class_id ) {
			$params['classId'] = $this->class_id;
		}

		if ( null !== $this->type_id ) {
			$params['typeId'] = $this->type_id;
		}

		if ( null !== $this->time_related_filters_year ) {
			$params['timeRelatedFiltersYear'] = $this->time_related_filters_year;
		}

		if ( null !== $this->time_related_filters_period_number ) {
			$params['timeRelatedFiltersPeriodNumber'] = $this->time_related_filters_period_number;
		}

		if ( null !== $this->first_use_from ) {
			$params['firstUseFrom'] = $this->first_use_from->format( 'Y-m-d\TH:i:s' );
		}

		if ( null !== $this->first_use_to ) {
			$params['firstUseTo'] = $this->first_use_to->format( 'Y-m-d\TH:i:s' );
		}

		if ( null !== $this->investment_from ) {
			$params['investmentFrom'] = $this->investment_from->format( 'Y-m-d\TH:i:s' );
		}

		if ( null !== $this->investment_to ) {
			$params['investmentTo'] = $this->investment_to->format( 'Y-m-d\TH:i:s' );
		}

		if ( null !== $this->amount_from ) {
			$params['amountFrom'] = $this->amount_from;
		}

		if ( null !== $this->amount_to ) {
			$params['amountTo'] = $this->amount_to;
		}

		if ( null !== $this->fields ) {
			$params['fields'] = $this->fields;
		}

		if ( empty( $params ) ) {
			return '';
		}

		return '?' . \http_build_query( $params );
	}
}
