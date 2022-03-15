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
	/**
	 * Construct declaration summary.
	 * 
	 * @param string $id                  ID.
	 * @param string $document_code       Document code.
	 * @param string $name                Name.
	 * @param string $document_time_frame Document time frame.
	 * @param string $status              Status.
	 * @param string $assignee            Assignee.
	 * @param string $company             Company.
	 */
	public function __construct( $id, $document_code, $name, $document_time_frame, $status, $assignee, $company ) {
		$this->id                  = $id;
		$this->document_code       = $document_code;
		$this->name                = $name;
		$this->document_time_frame = $document_time_frame;
		$this->status              = $status;
		$this->assignee            = $assignee;
		$this->company             = $company;
	}

	/**
	 * Get ID.
	 * 
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get document code.
	 * 
	 * @return string
	 */
	public function get_document_code() {
		return $this->document_code;
	}

	/**
	 * Get name.
	 * 
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get document time frame.
	 * 
	 * @return string
	 */
	public function get_document_time_frame() {
		return $this->document_time_frame;
	}

	/**
	 * Get status.
	 * 
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Get assignee.
	 * 
	 * @return string
	 */
	public function get_assignee() {
		return $this->assignee;
	}

	/**
	 * Get copmany.
	 * 
	 * @return string
	 */
	public function get_company() {
		return $this->company;
	}

	/**
	 * Create declartion summary from Twinfield object.
	 * 
	 * @param Organisation $organisation Organiation.
	 * @param object       $object       Object.
	 */
	public static function from_twinfield_object( $organisation, $object ) {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Twinfield vaiable name.

		$assignee = $organisation->new_user( $object->Assignee->Code );
		$assignee->set_name( $object->Assignee->Name );

		$company = $organisation->office( $object->Company->Code );
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

		// phpcs:enable
	}
}
