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
	private $daybook;

	/**
	 * Deletion date.
	 * 
	 * @var DateTimeInterface
	 */
	private $deletion_date;

	/**
	 * Reason for deletion.
	 * 
	 * @var string
	 */
	private $reason_for_deletion;

	/**
	 * Transaction date.
	 * 
	 * @var DateTimeInterface
	 */
	private $transaction_date;

	/**
	 * Transaction number.
	 * 
	 * @var string
	 */
	private $transaction_number;

	/**
	 * User.
	 * 
	 * @var string
	 */
	private $user;

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
	 * @param object $object Object.
	 * @return self
	 */
	public static function from_twinfield_object( $object ) {
		$data = new ObjectAccess( $object );

		$item = new self();

		$item->daybook             = $data->get_property( 'Daybook' );
		$item->deletion_date       = new DateTimeImmutable( $data->get_property( 'DeletionDate' ), new DateTimeZone( 'UTC' ) );
		$item->reason_for_deletion = $data->get_property( 'ReasonForDeletion' );
		$item->transaction_date    = new DateTimeImmutable( $data->get_property( 'TransactionDate' ), new DateTimeZone( 'UTC' ) );
		$item->transaction_number  = $data->get_property( 'TransactionNumber' );
		$item->user                = $data->get_property( 'User' );

		return $item;
	}
}
