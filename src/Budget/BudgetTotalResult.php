<?php
/**
 * Get budget total result.
 *
 * @since 1.0.0
 * @package Pronamic/WP/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Budget;

use JsonSerializable;
use Pronamic\WordPress\Twinfield\Hierarchies\HierarchyNode;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Get budget total result.
 *
 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Masters/Budget
 * @since 1.0.0
 * @package Pronamic/WP/Twinfield
 * @author Remco Tolsma <info@remcotolsma.nl>
 */
class BudgetTotalResult implements JsonSerializable {
	/**
	 * Year.
	 *
	 * @var int
	 */
	private $year;

	/**
	 * Period.
	 *
	 * @var int
	 */
	private $period;

	/**
	 * Get year.
	 *
	 * @return int
	 */
	public function get_year() {
		return $this->year;
	}

	/**
	 * Get period.
	 *
	 * @return int
	 */
	public function get_period() {
		return $this->period;
	}

	/**
	 * Get budget total.
	 *
	 * @return float
	 */
	public function get_budget_total() {
		return \floatval( $this->budget_total );
	}

	/**
	 * Get dimension 1 code.
	 *
	 * @return string
	 */
	public function get_dimension_1_code() {
		return $this->dimension_1_code;
	}

	/**
	 * Get dimension 2 code.
	 *
	 * @return string
	 */
	public function get_dimension_2_code() {
		return $this->dimension_2_code;
	}

	/**
	 * Get dimension 3 code.
	 *
	 * @return string
	 */
	public function get_dimension_3_code() {
		return $this->dimension_3_code;
	}

	/**
	 * Get group 1 code.
	 *
	 * @return string
	 */
	public function get_group_1_code() {
		return $this->group_1_code;
	}

	/**
	 * Get group 2 code.
	 *
	 * @return string
	 */
	public function get_group_2_code() {
		return $this->group_2_code;
	}

	/**
	 * Get group 3 code.
	 *
	 * @return string
	 */
	public function get_group_3_code() {
		return $this->group_3_code;
	}

	/**
	 * Check if this budget total result is part of the specified hierachy node.
	 *
	 * @param HierarchyNode $node Node.
	 * @return boolean True if is part, false otherwise.
	 */
	public function is_party_of_hierarchy_node( $node ) {
		$group_code = $node->get_code();

		$is_part_of_group_code = $this->is_part_of_group_code( $node->get_code() );

		if ( $is_part_of_group_code ) {
			return true;
		}

		foreach ( $node->get_accounts() as $account ) {
			$is_part_of_dimension_code = $this->is_part_of_dimension_code( $account->get_code() );

			if ( $is_part_of_dimension_code ) {
				return true;
			}
		}

		foreach ( $node->get_child_nodes() as $child_node ) {
			$is_part_of_child_node = $this->is_party_of_hierarchy_node( $child_node );

			if ( $is_part_of_child_node ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if this budget total result is part of the specified group code.
	 *
	 * @param string $code Group code.
	 * @return boolean True if is part, false otherwise.
	 */
	public function is_part_of_group_code( $code ) {
		if ( $code === $this->get_group_1_code() ) {
			return true;
		}

		if ( $code === $this->get_group_2_code() ) {
			return true;
		}

		if ( $code === $this->get_group_3_code() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if this budget total result is part of the specified dimension code.
	 *
	 * @param string $code Dimension code.
	 * @return boolean True if is part, false otherwise.
	 */
	public function is_part_of_dimension_code( $code ) {
		if ( $code === $this->get_dimension_1_code() ) {
			return true;
		}

		if ( $code === $this->get_dimension_2_code() ) {
			return true;
		}

		if ( $code === $this->get_dimension_3_code() ) {
			return true;
		}

		return false;
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'year'             => $this->year,
			'period'           => $this->period,
			'dimension_1_code' => $this->dimension_1_code,
			'dimension_2_code' => $this->dimension_2_code,
			'dimension_3_code' => $this->dimension_3_code,
			'group_1_code'     => $this->group_1_code,
			'group_2_code'     => $this->group_2_code,
			'group_3_code'     => $this->group_3_code,
			'actual_credit'    => $this->actual_credit,
			'actual_debit'     => $this->actual_debit,
			'actual_total'     => $this->actual_total,
			'budget_credit'    => $this->budget_credit,
			'budget_debit'     => $this->budget_debit,
			'budget_total'     => $this->budget_total,
		];
	}

	/**
	 * Create budget total result from Twinfield object.
	 *
	 * @param object $object Object.
	 * @return self
	 */
	public static function from_twinfield_object( $object ) {
		$data = ObjectAccess::from_object( $object );

		$result = new self();

		$result->year   = $data->get_property( 'Year' );
		$result->period = $data->get_property( 'Period' );

		$result->dimension_1_code = $data->get_property( 'Dim1' );
		$result->dimension_2_code = $data->get_property( 'Dim2' );
		$result->dimension_3_code = $data->get_property( 'Dim3' );

		$result->group_1_code = $data->get_property( 'Group1' );
		$result->group_2_code = $data->get_property( 'Group2' );
		$result->group_3_code = $data->get_property( 'Group3' );

		$result->actual_credit = $data->get_property( 'ActualCredit' );
		$result->actual_debit  = $data->get_property( 'ActualDebit' );
		$result->actual_total  = $data->get_property( 'ActualTotal' );

		$result->budget_credit = $data->get_property( 'BudgetCredit' );
		$result->budget_debit  = $data->get_property( 'BudgetDebit' );
		$result->budget_total  = $data->get_property( 'BudgetTotal' );

		return $result;
	}
}
