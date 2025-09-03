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
	 * @var string
	 */
	public string $status;

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
	 * Construct fixed asset.
	 *
	 * @param string        $id          ID.
	 * @param string        $name        Name.
	 * @param string        $description Description.
	 * @param HierarchyNode $root_node   Root node.
	 */
	public function __construct( $id, $status, $code, $description ) {
		$this->id          = $id;
		$this->status      = $status;
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
			$data->get_property( 'status' ),
			$data->get_property( 'code' ),
			$data->get_property( 'description' )
		);

		return $fixed_asset;
	}

	/**
	 * From JSON object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_json_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$fixed_asset = new self(
			$data->get_property( 'id' ),
			$data->get_property( 'status' ),
			$data->get_property( 'code' ),
			$data->get_property( 'description' )
		);

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

		return self::from_json_object( $data );
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'id'          => $this->id,
			'status'      => $this->status,
			'code'        => $this->code,
			'description' => $this->description,
		];
	}
}
