<?php
/**
 * Declarations Summary
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Declarations;

/**
 * Declarations Summary
 *
 * This class connects to the Twinfield declarations Webservices.
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class DeclarationSummary {
	public function __construct( $id, $document_code, $name, $document_time_frame, $status, $assignee, $company ) {
		$this->id                  = $id;
		$this->document_code       = $document_code;
		$this->name                = $name;
		$this->document_time_frame = $document_time_frame;
		$this->status              = $status;
		$this->assignee            = $assignee;
		$this->company             = $company;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_document_code() {
		return $this->document_code;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_document_time_frame() {
		return $this->document_time_frame;
	}

	public function get_status() {
		return $this->status;
	}

	public function get_assignee() {
		return $this->assignee;
	}

	public function get_company() {
		return $this->company;
	}

	public static function from_twinfield_object( $organisation, $object ) {
		$assignee = $organisation->new_user( $object->Assignee->Code );
		$assignee->set_name( $object->Assignee->Name );

		$company = $organisation->new_office( $object->Company->Code );
		$company->set_name( $object->Company->Name );

		return new self(
			$object->Id,
			$object->DocumentCode,
			$object->Name,
			DocumentTimeFrame::from_twinfield_object( $object->DocumentTimeFrame ),
			Status::from_twinfield_object( $object->Status ),
			$assignee,
			$company
		);
	}
}
