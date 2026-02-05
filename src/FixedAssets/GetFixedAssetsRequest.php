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
	#[RemoteParameterName( 'offset' )]
	public ?int $offset = null;

	/**
	 * Limit.
	 *
	 * @var int|null
	 */
	#[RemoteParameterName( 'limit' )]
	public ?int $limit = null;

	/**
	 * Order by.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'orderBy' )]
	public ?string $order_by = null;

	/**
	 * Ascending.
	 *
	 * @var bool|null
	 */
	#[RemoteParameterName( 'asc' )]
	public ?bool $asc = null;

	/**
	 * Search pattern.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'searchPattern' )]
	public ?string $search_pattern = null;

	/**
	 * Status.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'status' )]
	public ?string $status = null;

	/**
	 * Regime.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'regime' )]
	public ?string $regime = null;

	/**
	 * Tax reason.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'taxReason' )]
	public ?string $tax_reason = null;

	/**
	 * Location ID.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'locationId' )]
	public ?string $location_id = null;

	/**
	 * Group ID.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'groupId' )]
	public ?string $group_id = null;

	/**
	 * Class ID.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'classId' )]
	public ?string $class_id = null;

	/**
	 * Type ID.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'typeId' )]
	public ?string $type_id = null;

	/**
	 * Time related filters year.
	 *
	 * @var int|null
	 */
	#[RemoteParameterName( 'timeRelatedFiltersYear' )]
	public ?int $time_related_filters_year = null;

	/**
	 * Time related filters period number.
	 *
	 * @var int|null
	 */
	#[RemoteParameterName( 'timeRelatedFiltersPeriodNumber' )]
	public ?int $time_related_filters_period_number = null;

	/**
	 * First use from.
	 *
	 * @var \DateTimeInterface|null
	 */
	#[RemoteParameterName( 'firstUseFrom' )]
	public ?\DateTimeInterface $first_use_from = null;

	/**
	 * First use to.
	 *
	 * @var \DateTimeInterface|null
	 */
	#[RemoteParameterName( 'firstUseTo' )]
	public ?\DateTimeInterface $first_use_to = null;

	/**
	 * Investment from.
	 *
	 * @var \DateTimeInterface|null
	 */
	#[RemoteParameterName( 'investmentFrom' )]
	public ?\DateTimeInterface $investment_from = null;

	/**
	 * Investment to.
	 *
	 * @var \DateTimeInterface|null
	 */
	#[RemoteParameterName( 'investmentTo' )]
	public ?\DateTimeInterface $investment_to = null;

	/**
	 * Amount from.
	 *
	 * @var float|null
	 */
	#[RemoteParameterName( 'amountFrom' )]
	public ?float $amount_from = null;

	/**
	 * Amount to.
	 *
	 * @var float|null
	 */
	#[RemoteParameterName( 'amountTo' )]
	public ?float $amount_to = null;

	/**
	 * Fields.
	 *
	 * @var string|null
	 */
	#[RemoteParameterName( 'fields' )]
	public ?string $fields = null;

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

		$reflection = new \ReflectionClass( $this );

		foreach ( $reflection->getProperties() as $property ) {
			$value = $property->getValue( $this );

			if ( null === $value ) {
				continue;
			}

			$attributes = $property->getAttributes( RemoteParameterName::class );

			foreach ( $attributes as $attribute_reflection ) {
				$attribute = $attribute_reflection->newInstance();
				$param_name = $attribute->name;

				if ( $value instanceof \DateTimeInterface ) {
					$params[ $param_name ] = $value->format( 'Y-m-d\TH:i:s' );
				} elseif ( \is_bool( $value ) ) {
					$params[ $param_name ] = $value ? 'true' : 'false';
				} else {
					$params[ $param_name ] = $value;
				}
			}
		}

		if ( empty( $params ) ) {
			return '';
		}

		return '?' . \http_build_query( $params );
	}
}
