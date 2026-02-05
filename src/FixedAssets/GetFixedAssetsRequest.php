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
	 * @return static
	 */
	public function offset( int $offset ): static {
		$this->offset = $offset;
		return $this;
	}

	/**
	 * Set limit.
	 *
	 * @param int $limit Limit.
	 * @return static
	 */
	public function limit( int $limit ): static {
		$this->limit = $limit;
		return $this;
	}

	/**
	 * Set order by.
	 *
	 * @param string $order_by Order by.
	 * @return static
	 */
	public function orderBy( string $order_by ): static {
		$this->order_by = $order_by;
		return $this;
	}

	/**
	 * Set ascending.
	 *
	 * @param bool $asc Ascending.
	 * @return static
	 */
	public function asc( bool $asc = true ): static {
		$this->asc = $asc;
		return $this;
	}

	/**
	 * Set descending.
	 *
	 * @return static
	 */
	public function desc(): static {
		$this->asc = false;
		return $this;
	}

	/**
	 * Set search pattern.
	 *
	 * @param string $search_pattern Search pattern.
	 * @return static
	 */
	public function pattern( string $search_pattern ): static {
		$this->search_pattern = $search_pattern;
		return $this;
	}

	/**
	 * Set status.
	 *
	 * @param string $status Status.
	 * @return static
	 */
	public function status( string $status ): static {
		$this->status = $status;
		return $this;
	}

	/**
	 * Set regime.
	 *
	 * @param string $regime Regime.
	 * @return static
	 */
	public function regime( string $regime ): static {
		$this->regime = $regime;
		return $this;
	}

	/**
	 * Set tax reason.
	 *
	 * @param string $tax_reason Tax reason.
	 * @return static
	 */
	public function taxReason( string $tax_reason ): static {
		$this->tax_reason = $tax_reason;
		return $this;
	}

	/**
	 * Set location ID.
	 *
	 * @param string $location_id Location ID.
	 * @return static
	 */
	public function locationId( string $location_id ): static {
		$this->location_id = $location_id;
		return $this;
	}

	/**
	 * Set group ID.
	 *
	 * @param string $group_id Group ID.
	 * @return static
	 */
	public function groupId( string $group_id ): static {
		$this->group_id = $group_id;
		return $this;
	}

	/**
	 * Set class ID.
	 *
	 * @param string $class_id Class ID.
	 * @return static
	 */
	public function classId( string $class_id ): static {
		$this->class_id = $class_id;
		return $this;
	}

	/**
	 * Set type ID.
	 *
	 * @param string $type_id Type ID.
	 * @return static
	 */
	public function typeId( string $type_id ): static {
		$this->type_id = $type_id;
		return $this;
	}

	/**
	 * Set time related filters year.
	 *
	 * @param int $year Year.
	 * @return static
	 */
	public function year( int $year ): static {
		$this->time_related_filters_year = $year;
		return $this;
	}

	/**
	 * Set time related filters period number.
	 *
	 * @param int $period_number Period number.
	 * @return static
	 */
	public function period( int $period_number ): static {
		$this->time_related_filters_period_number = $period_number;
		return $this;
	}

	/**
	 * Set first use from.
	 *
	 * @param \DateTimeInterface $first_use_from First use from.
	 * @return static
	 */
	public function firstUseFrom( \DateTimeInterface $first_use_from ): static {
		$this->first_use_from = $first_use_from;
		return $this;
	}

	/**
	 * Set first use to.
	 *
	 * @param \DateTimeInterface $first_use_to First use to.
	 * @return static
	 */
	public function firstUseTo( \DateTimeInterface $first_use_to ): static {
		$this->first_use_to = $first_use_to;
		return $this;
	}

	/**
	 * Set investment from.
	 *
	 * @param \DateTimeInterface $investment_from Investment from.
	 * @return static
	 */
	public function investmentFrom( \DateTimeInterface $investment_from ): static {
		$this->investment_from = $investment_from;
		return $this;
	}

	/**
	 * Set investment to.
	 *
	 * @param \DateTimeInterface $investment_to Investment to.
	 * @return static
	 */
	public function investmentTo( \DateTimeInterface $investment_to ): static {
		$this->investment_to = $investment_to;
		return $this;
	}

	/**
	 * Set amount from.
	 *
	 * @param float $amount_from Amount from.
	 * @return static
	 */
	public function amountFrom( float $amount_from ): static {
		$this->amount_from = $amount_from;
		return $this;
	}

	/**
	 * Set amount to.
	 *
	 * @param float $amount_to Amount to.
	 * @return static
	 */
	public function amountTo( float $amount_to ): static {
		$this->amount_to = $amount_to;
		return $this;
	}

	/**
	 * Set fields.
	 *
	 * @param string $fields Fields.
	 * @return static
	 */
	public function fields( string $fields ): static {
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
