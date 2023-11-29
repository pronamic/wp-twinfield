<?php
/**
 * Status
 *
 * @since   1.0.0
 *
 * @package Pronamic/WP/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Declarations;

/**
 * Status
 *
 * @since   1.0.0
 * @package Pronamic/WP/Twinfield
 * @author  Remco Tolsma <info@remcotolsma.nl>
 */
class Status {
	/**
	 * Construct status.
	 * 
	 * @param string $description       Description.
	 * @param string $step_index        Step index.
	 * @param string $extra_information Extra information.
	 */
	public function __construct( $description, $step_index, $extra_information ) {
		$this->description       = $description;
		$this->step_index        = $step_index;
		$this->extra_information = $extra_information;
	}

	/**
	 * Get description.
	 * 
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Get step index.
	 * 
	 * @return string
	 */
	public function get_step_index() {
		return $this->step_index;
	}

	/**
	 * Get extra information.
	 * 
	 * @return string
	 */
	public function get_extra_information() {
		return $this->extra_information;
	}

	/**
	 * Create status from Twinfield object.
	 * 
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_twinfield_object( $value ) {
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Twinfield vaiable name.
		return new self( $value->Description, $value->StepIndex, $value->ExtraInformation );
	}
}
