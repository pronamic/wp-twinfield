<?php
/**
 * Hierarchy Node
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use IteratorAggregate;
use JsonSerializable;
use Pronamic\WordPress\Twinfield\Utility\ObjectAccess;

/**
 * Hierarchy Node
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
final class HierarchyNode implements IteratorAggregate, JsonSerializable, \Stringable {
	/**
	 * Internal id.
	 *
	 * @var int
	 */
	private $id;

	/**
	 * The code of the hierarchy node.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * The name of the hierarchy node.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The description of the hierarchy node.
	 *
	 * @var string
	 */
	private $description;

	/**
	 * The dimensions linked to the hierarchy node.
	 *
	 * @var HierarchyAccount[]
	 */
	private $accounts;

	/**
	 * The child nodes of the hierarchy node.
	 *
	 * @var array<string, HierarchyNode>
	 */
	private $child_nodes;

	/**
	 * Messages
	 *
	 * @var object|null
	 */
	private $messages;

	/**
	 * The number of times the hierarchy node was changed.
	 *
	 * @var int
	 */
	private $touched;

	/**
	 * Construct hierarchy node.
	 *
	 * @param int    $id          ID.
	 * @param string $code        Code.
	 * @param string $name        Name.
	 * @param string $description Description.
	 */
	public function __construct( $id, $code, $name, $description ) {
		$this->id          = $id;
		$this->code        = $code;
		$this->name        = $name;
		$this->description = $description;
		$this->accounts    = [];
		$this->child_nodes = [];
		$this->touched     = 0;
	}

	/**
	 * Get code.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
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
	 * Add account.
	 *
	 * @param HierarchyAccount $account Hierarchy account.
	 */
	public function add_account( HierarchyAccount $account ) {
		$this->accounts[] = $account;
	}

	/**
	 * Get accounts.
	 *
	 * @return HierarchyAccount[]
	 */
	public function get_accounts() {
		return $this->accounts;
	}

	/**
	 * Get accounts recursive.
	 *
	 * @return HierarchyAccount[]
	 */
	public function get_accounts_recursive() {
		$accounts = $this->accounts;

		foreach ( $this->child_nodes as $node ) {
			$child_accounts = $node->get_accounts_recursive();

			$accounts = array_merge( $accounts, $child_accounts );
		}

		return $accounts;
	}

	/**
	 * Has accounts
	 *
	 * @return bool True if node has accounts, false otherwise.
	 */
	public function has_accounts() {
		return \count( $this->accounts ) > 0;
	}

	/**
	 * Filter accounts.
	 *
	 * @param callable $callback The callback function to use.
	 * @return self
	 */
	public function filter_accounts( $callback = null ) {
		foreach ( $this->child_nodes as $node ) {
			$node->filter_accounts( $callback );
		}

		$this->accounts = \array_filter( $this->accounts, $callback );

		return $this;
	}

	/**
	 * Sort accounts.
	 *
	 * @param callable $callback The callback function to use.
	 * @return self
	 */
	public function sort_accounts( $callback ) {
		foreach ( $this->child_nodes as $node ) {
			$node->sort_accounts( $callback );
		}

		usort( $this->accounts, $callback );

		return $this;
	}

	/**
	 * Add child node.
	 *
	 * @param HierarchyNode $child_node Child node.
	 */
	public function add_child_node( HierarchyNode $child_node ) {
		$this->child_nodes[ $child_node->get_code() ] = $child_node;
	}

	/**
	 * Get child nodes.
	 *
	 * @return array<string, HierarchyNode>
	 */
	public function get_child_nodes() {
		return $this->child_nodes;
	}

	/**
	 * Has child nodes.
	 *
	 * @return bool True if node has child nodes, false otherwise.
	 */
	public function has_child_nodes() {
		return \count( $this->child_nodes ) > 0;
	}

	/**
	 * Filter nodes.
	 *
	 * @param callable $callback The callback function to use.
	 * @return self
	 */
	public function filter_nodes( $callback = null ) {
		foreach ( $this->child_nodes as $node ) {
			$node->filter_nodes( $callback );
		}

		$this->child_nodes = \array_filter( $this->child_nodes, $callback );

		return $this;
	}

	/**
	 * Sort nodes.
	 *
	 * @param callable $callback The callback function to use.
	 * @return self
	 */
	public function sort_nodes( $callback ) {
		foreach ( $this->child_nodes as $node ) {
			$node->sort_nodes( $callback );
		}

		usort( $this->child_nodes, $callback );

		return $this;
	}

	/**
	 * Get child nodes recursive.
	 *
	 * @return array<string, HierarchyNode>
	 */
	public function get_child_nodes_recursive() {
		$nodes = [];

		foreach ( $this->child_nodes as $node ) {
			$nodes[ $node->get_code() ] = $node;

			$nodes = array_merge( $nodes, $node->get_child_nodes_recursive() );
		}

		return $nodes;
	}

	/**
	 * Get iterator.
	 *
	 * @return HierarchyNodeIterator
	 */
	public function getIterator() {
		return new HierarchyNodeIterator( $this->child_nodes );
	}

	/**
	 * String.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return \sprintf(
			'%s - %s',
			$this->code,
			$this->name
		);
	}

	/**
	 * Convert from Twinfield object.
	 *
	 * @param object $item Object.
	 * @return self
	 */
	public static function from_twinfield_object( $item ) {
		$data = ObjectAccess::from_object( $item );

		$hierarchy = new self(
			$data->get_property( 'Id' ),
			$data->get_property( 'Code' ),
			$data->get_property( 'Name' ),
			$data->get_property( 'Description' )
		);

		/**
		 * Accounts.
		 */

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( \property_exists( $item, 'Accounts' ) && \property_exists( $item->Accounts, 'HierarchyAccount' ) ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$hierarchy_accounts = $item->Accounts->HierarchyAccount;

			if ( \is_object( $hierarchy_accounts ) ) {
				$hierarchy_accounts = [ $hierarchy_accounts ];
			}

			foreach ( $hierarchy_accounts as $o ) {
				$hierarchy->add_account( HierarchyAccount::from_twinfield_object( $o ) );
			}
		}

		/**
		 * Child nodes.
		 */

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( \property_exists( $item, 'ChildNodes' ) && \property_exists( $item->ChildNodes, 'HierarchyNode' ) ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$child_nodes = $item->ChildNodes->HierarchyNode;

			if ( \is_object( $child_nodes ) ) {
				$child_nodes = [ $child_nodes ];
			}

			foreach ( $child_nodes as $o ) {
				$hierarchy->add_child_node( self::from_twinfield_object( $o ) );
			}
		}

		/**
		 * Done.
		 */
		return $hierarchy;
	}

	/**
	 * From JSON object.
	 *
	 * @param object $value Object.
	 * @return self
	 */
	public static function from_json_object( $value ) {
		$data = ObjectAccess::from_object( $value );

		$hierarchy = new self(
			$data->get_property( 'id' ),
			$data->get_property( 'code' ),
			$data->get_property( 'name' ),
			$data->get_property( 'description' )
		);

		foreach ( $data->get_property( 'accounts' ) as $item ) {
			$hierarchy->add_account( HierarchyAccount::from_json_object( $item ) );
		}

		foreach ( $data->get_property( 'child_nodes' ) as $item ) {
			$hierarchy->add_child_node( self::from_json_object( $item ) );
		}

		return $hierarchy;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'id'          => $this->id,
			'code'        => $this->code,
			'name'        => $this->name,
			'description' => $this->description,
			'accounts'    => $this->accounts,
			'child_nodes' => \array_values( $this->child_nodes ),
		];
	}
}
