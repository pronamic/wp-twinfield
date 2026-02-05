<?php
/**
 * Fixed asset
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\FixedAssets;

use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Fixed asset class
 */
final class FixedAsset {
	/**
	 * ID.
	 *
	 * @var string
	 */
	public string $id;

	/**
	 * Status.
	 *
	 * @var string|null
	 */
	public ?string $status;

	/**
	 * Posting error message.
	 *
	 * @var string|null
	 */
	public ?string $posting_error_message;

	/**
	 * Code.
	 *
	 * @var string
	 */
	public string $code;

	/**
	 * Description.
	 *
	 * @var string
	 */
	public string $description;

	/**
	 * Last depreciated period.
	 *
	 * @var TimePeriod|null
	 */
	public ?TimePeriod $last_depreciated_period;

	/**
	 * Investment date.
	 *
	 * @var \DateTimeInterface|null
	 */
	public ?\DateTimeInterface $investment_date;

	/**
	 * First use date.
	 *
	 * @var \DateTimeInterface|null
	 */
	public ?\DateTimeInterface $first_use_date;

	/**
	 * Dispose date.
	 *
	 * @var \DateTimeInterface|null
	 */
	public ?\DateTimeInterface $dispose_date;

	/**
	 * Youngest balances.
	 *
	 * @var YoungestBalances|null
	 */
	public ?YoungestBalances $youngest_balances;

	/**
	 * Dispose price.
	 *
	 * @var float|null
	 */
	public ?float $dispose_price;

	/**
	 * Version.
	 *
	 * @var int|null
	 */
	public ?int $version;

	/**
	 * Group ID.
	 *
	 * @var string|null
	 */
	public ?string $group_id;

	/**
	 * Construct fixed asset.
	 *
	 * @param string $id          ID.
	 * @param string $code        Code.
	 * @param string $description Description.
	 */
	public function __construct( string $id, string $code, string $description ) {
		$this->id          = $id;
		$this->code        = $code;
		$this->description = $description;
	}

	/**
	 * Convert from Twinfield object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$fixed_asset = new self(
			$data->get_property( 'id' ),
			$data->get_property( 'code' ),
			$data->get_property( 'description' )
		);

		$fixed_asset->status                  = $data->get_optional( 'status' );
		$fixed_asset->posting_error_message   = $data->get_optional( 'postingErrorMessage' );
		$fixed_asset->last_depreciated_period = $data->get_optional( 'lastDepreciatedPeriod' );
		$fixed_asset->investment_date         = $data->get_optional( 'investmentDate' );
		$fixed_asset->first_use_date          = $data->get_optional( 'firstUseDate' );
		$fixed_asset->dispose_date            = $data->get_optional( 'disposeDate' );
		$fixed_asset->youngest_balances       = $data->get_optional( 'youngestBalances' );
		$fixed_asset->dispose_price           = $data->get_optional( 'disposePrice' );
		$fixed_asset->version                 = $data->get_optional( 'version' );
		$fixed_asset->group_id                = $data->get_optional( 'groupId' );

		return $fixed_asset;
	}

	/**
	 * From object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$fixed_asset = new self(
			$data->get_property( 'id' ),
			$data->get_property( 'code' ),
			$data->get_property( 'description' )
		);

		$fixed_asset->status                  = $data->get_optional( 'status' );
		$fixed_asset->posting_error_message   = $data->get_optional( 'postingErrorMessage' );
		$fixed_asset->last_depreciated_period = TimePeriod::from_object( $data->get_optional( 'lastDepreciatedPeriod' ) );
		$fixed_asset->youngest_balances       = YoungestBalances::from_object( $data->get_optional( 'youngestBalances' ) );
		$fixed_asset->dispose_price           = $data->get_optional( 'disposePrice' );
		$fixed_asset->version                 = $data->get_optional( 'version' );
		$fixed_asset->group_id                = $data->get_optional( 'groupId' );

		// Parse dates.
		$investment_date_string = $data->get_optional( 'investmentDate' );
		if ( null !== $investment_date_string ) {
			$fixed_asset->investment_date = new \DateTimeImmutable( $investment_date_string );
		}

		$first_use_date_string = $data->get_optional( 'firstUseDate' );
		if ( null !== $first_use_date_string ) {
			$fixed_asset->first_use_date = new \DateTimeImmutable( $first_use_date_string );
		}

		$dispose_date_string = $data->get_optional( 'disposeDate' );
		if ( null !== $dispose_date_string ) {
			$fixed_asset->dispose_date = new \DateTimeImmutable( $dispose_date_string );
		}

		return $fixed_asset;
	}

	/**
	 * From JSON.
	 *
	 * @param string $value JSON.
	 * @return self
	 */
	public static function from_json( string $value ) {
		$data = \json_decode( $value );

		return self::from_object( $data );
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'id'                    => $this->id,
			'status'                => $this->status,
			'postingErrorMessage'   => $this->posting_error_message,
			'code'                  => $this->code,
			'description'           => $this->description,
			'lastDepreciatedPeriod' => $this->last_depreciated_period,
			'investmentDate'        => $this->investment_date?->format( 'Y-m-d\TH:i:s' ),
			'firstUseDate'          => $this->first_use_date?->format( 'Y-m-d\TH:i:s' ),
			'disposeDate'           => $this->dispose_date?->format( 'Y-m-d\TH:i:s' ),
			'youngestBalances'      => $this->youngest_balances,
			'disposePrice'          => $this->dispose_price,
			'version'               => $this->version,
			'groupId'               => $this->group_id,
		];
	}
}
