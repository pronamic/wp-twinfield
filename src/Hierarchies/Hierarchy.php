<?php
/**
 * Hierarchy
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Hierarchies;

use IteratorAggregate;
use JsonSerializable;

/**
 * Hierarchy
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class Hierarchy implements IteratorAggregate, JsonSerializable {
	/**
	 * The code of the hierarchy.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * The name of the hierarchy.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The description of the hierarchy.
	 *
	 * @var string
	 */
	private $description;

	/**
	 * The root node of the hierarchy.
	 *
	 * @var HierarchyNode
	 */
	private $root_node;

	/**
	 * The access rights to the hierarchy.
	 *
	 * @var AccessRights
	 */
	private $access_rights;

	/**
	 * The number of times the hierarchy was changed.
	 *
	 * @var int
	 */
	private $touched;

	/**
	 * Node map.
	 *
	 * @var array
	 */
	private $node_map;

	/**
	 * Construct hierarchy.
	 *
	 * @param string        $code        Code.
	 * @param string        $name        Name.
	 * @param string        $description Description.
	 * @param HierarchyNode $root_node   Root node.
	 */
	public function __construct( $code, $name, $description, $root_node ) {
		$this->code        = $code;
		$this->name        = $name;
		$this->description = $description;
		$this->root_node   = $root_node;
	}

	/**
	 * Get root node.
	 *
	 * @return HierarchyNode
	 */
	public function get_root_node() {
		return $this->root_node;
	}

	/**
	 * Build node map.
	 *
	 * @return array<string, HierarchyNode>
	 */
	private function build_node_map() {
		return $this->root_node->get_child_nodes_recursive();
	}

	/**
	 * Get node map.
	 *
	 * @link https://docs.php.earth/php/ref/oop/design-patterns/lazy-loading/
	 * @return array<string, HierarchyNode>
	 */
	public function get_node_map() {
		if ( null === $this->node_map ) {
			$this->node_map = $this->build_node_map();
		}

		return $this->node_map;
	}

	/**
	 * Get node by code.
	 *
	 * @param string $code Code.
	 * @return HierarchyNode|null
	 */
	public function get_node_by_code( $code ) {
		$node_map = $this->get_node_map();

		if ( array_key_exists( $code, $node_map ) ) {
			return $node_map[ $code ];
		}

		return null;
	}

	/**
	 * Get iterator.
	 *
	 * @return HierarchyNodeIterator
	 */
	public function getIterator() {
		return new HierarchyNodeAccountIterator( [ $this->root_node ] );
	}

	/**
	 * Convert from object.
	 *
	 * @param object $item Object.
	 * @return Hierarchy
	 */
	public static function from_object( $item ) {
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$hierarchy = new Hierarchy( $item->Code, $item->Name, $item->Description, HierarchyNode::from_object( $item->RootNode ) );

		return $hierarchy;
	}

	/**
	 * Serialize to JSON.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize() {
		return [
			'code'        => $this->code,
			'name'        => $this->name,
			'description' => $this->description,
			'root_node'   => $this->root_node,
		];
	}
}
