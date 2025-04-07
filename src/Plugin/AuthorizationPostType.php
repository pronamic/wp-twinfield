<?php
/**
 * Authorization Post Type
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;

/**
 * Authorization Post Type
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class AuthorizationPostType {
	public const KEY = 'pronamic_twf_auth';

	/**
	 * Plugin.
	 * 
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Construct authorization post type.
	 *
	 * @param Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'init', $this->init( ... ) );

		\add_action( 'add_meta_boxes', $this->add_meta_boxes( ... ), 10, 2 );

		\add_action( 'save_post_' . self::KEY, $this->maybe_set_default_authorization( ... ) );

		\add_action( 'save_post_' . self::KEY, $this->maybe_save_schedule( ... ) );

		\add_action( 'display_post_states', $this->display_post_states( ... ), 10, 2 );
	}

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	public function init() {
		\register_post_type(
			self::KEY,
			[
				'label'        => __( 'Twinfield Authorizations', 'pronamic-twinfield' ),
				'labels'       => [
					'name'                  => _x( 'Authorizations', 'post type general name', 'pronamic-twinfield' ),
					'singular_name'         => _x( 'Authorization', 'post type singular name', 'pronamic-twinfield' ),
					'add_new'               => _x( 'Add New', 'twinfield authorizations', 'pronamic-twinfield' ),
					'add_new_item'          => __( 'Add New Authorization', 'pronamic-twinfield' ),
					'edit_item'             => __( 'Edit Authorization', 'pronamic-twinfield' ),
					'new_item'              => __( 'New Authorization', 'pronamic-twinfield' ),
					'view_item'             => __( 'View Authorization', 'pronamic-twinfield' ),
					'view_items'            => __( 'View Authorizations', 'pronamic-twinfield' ),
					'search_items'          => __( 'Search Authorizations', 'pronamic-twinfield' ),
					'not_found'             => __( 'No authorizations found.', 'pronamic-twinfield' ),
					'not_found_in_trash'    => __( 'Not authorizations found in Trash.', 'pronamic-twinfield' ),
					'parent_item_colon'     => __( 'Parent Authorization:', 'pronamic-twinfield' ),
					'all_items'             => __( 'All Authorizations', 'pronamic-twinfield' ),
					'archives'              => __( 'Authorization Archives', 'pronamic-twinfield' ),
					'attributes'            => __( 'Authorization Attributes', 'pronamic-twinfield' ),
					'insert_into_item'      => __( 'Insert into authorization', 'pronamic-twinfield' ),
					'uploaded_to_this_item' => __( 'Uploaded to this authorization', 'pronamic-twinfield' ),
					'featured_image'        => __( 'Featured image', 'pronamic-twinfield' ),
					'set_featured_image'    => __( 'Set featured image', 'pronamic-twinfield' ),
					'remove_featured_image' => __( 'Remove featured image', 'pronamic-twinfield' ),
					'use_featured_image'    => __( 'Use as featured image', 'pronamic-twinfield' ),
					'filter_items_list'     => __( 'Filter authorizations list', 'pronamic-twinfield' ),
					'filter_by_date'        => __( 'Filter by date', 'pronamic-twinfield' ),
					'items_list_navigation' => __( 'Authorizations list navigation', 'pronamic-twinfield' ),
					'items_list'            => __( 'Authorizations list', 'pronamic-twinfield' ),
					'menu_name'             => __( 'Authorizations', 'pronamic-twinfield' ),
					'name_admin_bar'        => _x( 'Twinfield Authorization', 'add new from admin bar', 'pronamic-twinfield' ),
				],
				'public'       => true,
				/**
				 * Hierarchical is required for usage in `wp_dropdown_pages`.
				 * 
				 * @link https://developer.wordpress.org/reference/functions/register_post_type/#hierarchical
				 * @link https://developer.wordpress.org/reference/functions/wp_dropdown_pages/
				 */
				'hierarchical' => true,
				'show_in_menu' => false,
				'supports'     => [
					'slug',
				],
			]
		);
	}

	/**
	 * Add meta boxes.
	 * 
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Post object.
	 */
	public function add_meta_boxes( $post_type, $post ) {
		if ( self::KEY !== $post_type ) {
			return;
		}

		/**
		 * Authentication.
		 */
		$object = \json_decode( (string) $post->post_content );

		if ( ! empty( $object ) ) {
			$authentication = AuthenticationInfo::from_object( $object );

			if ( $authentication ) {
				\add_meta_box(
					'pronamic_twinfield_authentication',
					\__( 'Authentication', 'twinfield' ),
					$this->meta_box_authentication( ... ),
					$post_type,
					'normal',
					'high'
				);

				\add_meta_box(
					'pronamic_twinfield_offices',
					\__( 'Offices', 'twinfield' ),
					$this->meta_box_offices( ... ),
					$post_type,
					'normal',
					'high'
				);
			}
		}

		\add_meta_box(
			'pronamic_twinfield_authorize',
			\__( 'Authorize', 'twinfield' ),
			$this->meta_box_authorize( ... ),
			$post_type,
			'normal',
			'high'
		);

		\add_meta_box(
			'pronamic_twinfield_menu',
			\__( 'Menu', 'twinfield' ),
			$this->meta_box_menu( ... ),
			$post_type,
			'normal',
			'high'
		);

		\add_meta_box(
			'pronamic_twinfield_save_schedule',
			\__( 'Save schedule', 'pronamic-twinfield' ),
			$this->meta_box_save_schedule( ... ),
			$post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Maybe set default authorization.
	 * 
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/GatewayPostType.php#L42
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/GatewayPostType.php#L103-L124
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function maybe_set_default_authorization( $post_id ) {
		// Don't set the default authorization if the post is not published.
		if ( 'publish' !== \get_post_status( $post_id ) ) {
			return;
		}

		// Don't set the default gateway if there is already a published gateway set.
		$id = \get_option( 'pronamic_twinfield_authorization_post_id' );

		if ( ! empty( $id ) && 'publish' === \get_post_status( $id ) ) {
			return;
		}

		// Update.
		update_option( 'pronamic_twinfield_authorization_post_id', $post_id );
	}

	/**
	 * Display post states.
	 *
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/Admin/AdminGatewayPostType.php#L68
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/Admin/AdminGatewayPostType.php#L215-L233
	 * @param array    $post_states Post states.
	 * @param \WP_Post $post        Post.
	 * @return array
	 */
	public function display_post_states( $post_states, $post ) {
		if ( self::KEY !== \get_post_type( $post ) ) {
			return $post_states;
		}

		if ( (int) get_option( 'pronamic_twinfield_authorization_post_id' ) === $post->ID ) {
			$post_states['pronamic_twinfield_authorizatio_default'] = __( 'Default', 'pronamic-twinfield' );
		}

		return $post_states;
	}

	/**
	 * Meta box authorize.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @param array   $box  Box.
	 */
	public function meta_box_authorize( $post, $box ) {
		$plugin = $this->plugin;

		include __DIR__ . '/../../admin/meta-box-authorize.php';
	}

	/**
	 * Meta box authentication.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @param array   $box  Box.
	 */
	public function meta_box_authentication( $post, $box ) {
		$plugin = $this->plugin;

		include __DIR__ . '/../../admin/meta-box-authentication.php';
	}

	/**
	 * Meta box menu.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @param array   $box  Box.
	 */
	public function meta_box_menu( $post, $box ) {
		$plugin = $this->plugin;

		include __DIR__ . '/../../admin/meta-box-menu.php';
	}

	/**
	 * Get schedule data.
	 * 
	 * @return []
	 */
	private function get_schedule_data() {
		return [
			[
				'meta_key' => '_pronamic_twinfield_save_offices_schedule',
				'label'    => \__( 'Offices', 'pronamic-twinfield' ),
				'hook'     => 'pronamic_twinfield_pull_offices',
			],
			[
				'meta_key' => '_pronamic_twinfield_save_hierarchies_schedule',
				'label'    => \__( 'Hierarchies', 'pronamic-twinfield' ),
				'hook'     => 'pronamic_twinfield_save_hierarchies',
			],
			[
				'meta_key' => '_pronamic_twinfield_save_bank_statements_schedule',
				'label'    => \__( 'Bank statements', 'pronamic-twinfield' ),
				'hook'     => 'pronamic_twinfield_save_bank_statements',
			],
		];
	}

	/**
	 * Maybe save schedule.
	 * 
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/GatewayPostType.php#L42
	 * @link https://github.com/pronamic/wp-pay-core/blob/3.2.0/src/GatewayPostType.php#L103-L124
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function maybe_save_schedule( $post_id ) {
		if ( ! \array_key_exists( 'pronamic_twinfield_authorization_save_schedule_nonce', $_POST ) ) {
			return;
		}

		if ( ! \wp_verify_nonce( \sanitize_key( $_POST['pronamic_twinfield_authorization_save_schedule_nonce'] ), 'pronamic_twinfield_authorization_save_schedule' ) ) {
			return;
		}

		$data = $this->get_schedule_data();

		foreach ( $data as $item ) {
			$meta_key = $item['meta_key'];

			if ( \array_key_exists( $meta_key, $_POST ) ) {
				$meta_value = \sanitize_text_field( \wp_unslash( $_POST[ $meta_key ] ) );

				\as_unschedule_action(
					$item['hook'],
					[
						'authorization' => $post_id,
					],
					'pronamic-twinfield'
				);

				if ( empty( $meta_value ) ) {
					\delete_post_meta( $post_id, $meta_key );
				} else {
					\update_post_meta( $post_id, $meta_key, $meta_value );

					\as_schedule_cron_action(
						\time(),
						$meta_value,
						$item['hook'],
						[
							'authorization' => $post_id,
						],
						'pronamic-twinfield'
					);
				}
			}
		}
	}

	/**
	 * Meta box save schedule.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @param array   $box  Box.
	 */
	public function meta_box_save_schedule( $post, $box ) {
		$plugin = $this->plugin;

		\wp_nonce_field( 'pronamic_twinfield_authorization_save_schedule', 'pronamic_twinfield_authorization_save_schedule_nonce' );

		include __DIR__ . '/../../admin/meta-box-save-schedule.php';
	}

	/**
	 * Meta box offices.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @param array   $box  Box.
	 */
	public function meta_box_offices( $post, $box ) {
		$plugin = $this->plugin;

		include __DIR__ . '/../../admin/meta-box-offices.php';
	}
}
