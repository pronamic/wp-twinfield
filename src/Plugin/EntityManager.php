<?php
/**
 * Entity manager
 *
 * @package Pronamic/WordPress/Twinfield
 * @link    https://libreworks.github.io/xyster/documentation/guide/xyster.orm.setup.html
 * @link    https://redbeanphp.com/
 * @link    https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/php-mapping.html
 * @link    https://symfony.com/doc/current/doctrine.html#creating-an-entity-class
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

/**
 * Entity manager class
 */
class EntityManager {
	private $entities = [];

	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function register_entity( $class, $entity ) {
		$this->entities[ $class ] = $entity;
	}

	/**
	 * 
	 * Like `getManagerForClass`.
	 */
	public function get_entity( $object ) {
		$class = get_class( $object );

		if ( ! array_key_exists( $class, $this->entities ) ) {
			throw new \Exception( \sprintf( 'Unknow entity: %s', $class ) );
		}

		return $this->entities[ $class ];
	}

	public function first( $object, $condition ) {
		$entity = $this->get_entity( $object );

		$where_condition = [];

		foreach ( $condition as $key => $value ) {
			$where_condition[] = $key . ' = ' . $entity->format[ $key ];
		}

		$query = $this->wpdb->prepare(
			sprintf(
				'SELECT %s FROM %s WHERE %s LIMIT 1;',
				$entity->primary_key,
				$entity->table,
				implode( ' AND ', $where_condition )
			),
			$condition
		);

		$id = $this->wpdb->get_var( $query );

		return $id;
	}

	public function first_or_create( $object, $condition, $values ) {
		global $wpdb;
		
		$entity = $this->get_entity( $object );

		$id = $this->first( $object, $condition );

		if ( null === $id ) {
			$data = array_merge( $condition, $values );

			$result = $this->wpdb->insert(
				$entity->table,
				$data,
				array_intersect_key( $entity->format, $data )
			);

			if ( false === $result ) {
				throw new \Exception( \sprintf( 'Insert error: %s', $wpdb->last_error ) );
			}

			$id = $this->wpdb->insert_id;
		}

		return $id;
	}

	public function update_or_create( $object, $condition, $values ) {
		global $wpdb;

		$entity = $this->get_entity( $object );

		$id = $this->first( $object, $condition );

		if ( null !== $id ) {
			$result = $this->wpdb->update(
				$entity->table,
				$values,
				[
					$entity->primary_key => $id,
				],
			);

			if ( false === $result ) {
				throw new \Exception( \sprintf( 'Update error: %s', $this->wpdb->last_error ) );
			}
		}

		if ( null === $id ) {
			$data = array_merge( $condition, $values );

			$result = $this->wpdb->insert(
				$entity->table,
				$data,
				array_intersect_key( $entity->format, $data )
			);

			if ( false === $result ) {
				throw new \Exception( \sprintf( 'Insert error: %s', $this->wpdb->last_error ) );
			}

			$id = $this->wpdb->insert_id;
		}

		return $id;
	}
}
