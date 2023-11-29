<?php
/**
 * Entity manager
 *
 * @package Pronamic/WordPress/Twinfield
 * @link    https://libreworks.github.io/xyster/documentation/guide/xyster.orm.setup.html
 * @link    https://redbeanphp.com/
 * @link    https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/php-mapping.html
 * @link    https://symfony.com/doc/current/doctrine.html#creating-an-entity-class
 * @link    http://propelorm.org/documentation/reference/active-record.html
 * @link    https://www.baeldung.com/hibernate-entitymanager
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

/**
 * Entity manager class
 */
class EntityManager {
	/**
	 * Entities.
	 * 
	 * @var array
	 */
	private $entities = [];

	/**
	 * Construct entity manager.
	 * 
	 * @param wpdb $wpdb WordPress database access object.
	 */
	public function __construct( $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Register entity.
	 * 
	 * @param string $class_name Class name.
	 * @param Entity $entity     Entity.
	 */
	public function register_entity( $class_name, $entity ) {
		$this->entities[ $class_name ] = $entity;
	}

	/**
	 * Get entity for object.
	 * 
	 * Like `getManagerForClass`.
	 * 
	 * @param object $item Object.
	 * @return Entity
	 * @throws \Exception Throws exception if object class is unknown.
	 */
	public function get_entity( $item ) {
		$class_name = \get_class( $item );

		if ( ! \array_key_exists( $class_name, $this->entities ) ) {
			throw new \Exception(
				\sprintf(
					'Unknow entity: %s',
					\esc_html( $class_name )
				)
			);
		}

		return $this->entities[ $class_name ];
	}

	/**
	 * First.
	 * 
	 * @param object $item      Object.
	 * @param array  $condition Condition.
	 * @return int
	 */
	public function first( $item, $condition ) {
		$entity = $this->get_entity( $item );

		$where_condition = [];

		foreach ( $condition as $key => $value ) {
			$where_condition[] = $key . ' = ' . $entity->format[ $key ];
		}

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared

		// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber
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

		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

		return $id;
	}

	/**
	 * Insert.
	 * 
	 * @param Entity $entity Entity.
	 * @param array  $data   Data.
	 * @return int
	 * @throws \Exception Throws exception if insert fails.
	 */
	private function insert( $entity, $data ) {
		$data['created_at'] = \current_time( 'mysql', true );
		$data['updated_at'] = \current_time( 'mysql', true );

		$result = $this->wpdb->insert(
			$entity->table,
			$data,
			array_intersect_key( $entity->format, $data )
		);

		if ( false === $result ) {
			throw new \Exception(
				\sprintf(
					'Insert error: %s, data: %s.',
					\esc_html( $this->wpdb->last_error ),
					\wp_json_encode( $data, \JSON_PRETTY_PRINT )
				)
			);
		}

		$id = $this->wpdb->insert_id;

		return $id;
	}

	/**
	 * Update.
	 * 
	 * @param Entity $entity Entity.
	 * @param array  $data   Data.
	 * @param int    $id     ID.
	 * @return int
	 * @throws \Exception Throws exception if update fails.
	 */
	private function update( $entity, $data, $id ) {
		$data['updated_at'] = \current_time( 'mysql', true );

		$result = $this->wpdb->update(
			$entity->table,
			$data,
			[
				$entity->primary_key => $id,
			],
		);

		if ( false === $result ) {
			throw new \Exception(
				\sprintf(
					'Update error: %s',
					\esc_html( $this->wpdb->last_error )
				)
			);
		}

		return $id;
	}

	/**
	 * First or create.
	 * 
	 * @param object $item      Object.
	 * @param array  $condition Condition.
	 * @param array  $values    Values.
	 * @return int
	 */
	public function first_or_create( $item, $condition, $values ) {
		global $wpdb;
		
		$entity = $this->get_entity( $item );

		$id = $this->first( $item, $condition );

		if ( null === $id ) {
			$data = array_merge( $condition, $values );

			$id = $this->insert( $entity, $data );
		}

		return $id;
	}

	/**
	 * Update or create.
	 * 
	 * @param object $item      Object.
	 * @param array  $condition Condition.
	 * @param array  $values    Values.
	 * @return int
	 */
	public function update_or_create( $item, $condition, $values ) {
		global $wpdb;

		$entity = $this->get_entity( $item );

		$id = $this->first( $item, $condition );

		if ( null !== $id ) {
			$id = $this->update( $entity, $values, $id );
		}

		if ( null === $id ) {
			$data = array_merge( $condition, $values );

			$id = $this->insert( $entity, $data );
		}

		return $id;
	}
}
