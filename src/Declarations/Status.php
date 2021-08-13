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
	public function __construct( $description, $step_index, $extra_information ) {
		$this->description       = $description;
		$this->step_index        = $step_index;
		$this->extra_information = $extra_information;
	}

	public function get_description() {
		return $this->description;
	}

	public function get_step_index() {
		return $this->step_index;
	}

	public function get_extra_information() {
		return $this->extra_information;
	}

	public static function from_twinfield_object( $object ) {
		return new self( $object->Description, $object->StepIndex, $object->ExtraInformation );
	}
}
