<?php
/**
 * Deleted transaction
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield/Transactions
 */

namespace Pronamic\WordPress\Twinfield\Transactions;

use ArrayObject;
use DateTimeInterface;
use DateTimeImmutable;
use DateTimeZone;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Deleted transaction
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DeletedTransaction implements JsonSerializable {
	/**
	 * Daybook.
	 * 
	 * @var string
	 */
	public $daybook;

	/**
	 * Deletion date.
	 * 
	 * @var DateTimeInterface
	 */
	public $deletion_date;

	/**
	 * Reason for deletion.
	 * 
	 * @var string
	 */
	public $reason_for_deletion;

	/**
	 * Transaction date.
	 * 
	 * @var DateTimeInterface
	 */
	public $transaction_date;

	/**
	 * Transaction number.
	 * 
	 * @var string
	 */
	public $transaction_number;

	/**
	 * User.
	 * 
	 * @var string
	 */
	public $user;

	/**
	 * Construct.
	 */
	public function __construct( $daybook, $deletion_date, $reason_for_deletion, $transaction_date, $transaction_number, $user ) {
		$this->daybook             = $daybook;
		$this->deletion_date       = $deletion_date;
		$this->reason_for_deletion = $reason_for_deletion;
		$this->transaction_date    = $transaction_date;
		$this->transaction_number  = $transaction_number;
		$this->user                = $user;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return DeletedTransaction[]
	 */
	public function jsonSerialize() {
		return [
			'daybook'             => $this->daybook,
			'deletion_date'       => $this->deletion_date->format( \DATE_ATOM ),
			'reason_for_deletion' => $this->reason_for_deletion,
			'transaction_date'    => $this->transaction_date->format( \DATE_ATOM ),
			'transaction_number'  => $this->transaction_number,
			'user'                => $this->user,
		];
	}

	/**
	 * Create office list from XML.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		$data = new ObjectAccess( $value );

		$item = new self(
			$data->get_property( 'Daybook' ),
			new DateTimeImmutable( $data->get_property( 'DeletionDate' ), new DateTimeZone( 'UTC' ) ),
			$data->get_property( 'ReasonForDeletion' ),
			new DateTimeImmutable( $data->get_property( 'TransactionDate' ), new DateTimeZone( 'UTC' ) ),
			$data->get_property( 'TransactionNumber' ),
			$data->get_property( 'User' )
		);

		return $item;
	}
}
