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

	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	public function setup() {
		\add_action( 'init', [ $this, 'init' ] );

		\add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );

		\add_action( 'save_post_' . self::KEY, [ $this, 'maybe_set_default_authorization' ] );

		\add_action( 'display_post_states', [ $this, 'display_post_states' ], 10, 2 );
	}

	public function init() {
		\register_post_type(
			self::KEY,
			[
				'label'        => __( 'Twinfield Authorizations', 'pronamic-twinfield' ),
				'labels'       => [
					'menu_name' => __( 'Authorizations', 'pronamic-twinfield' ),
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
		 * Authentication/
		 */
		$object = \json_decode( $post->post_content );

		if ( ! empty( $object ) ) {
			$authentication = AuthenticationInfo::from_object( $object );

			if ( $authentication ) {
				\add_meta_box(
					'pronamic_twinfield_authentication',
					\__( 'Authentication', 'twinfield' ),
					[ $this, 'meta_box_authentication' ],
					$post_type,
					'normal',
					'high'
				);

				\add_meta_box(
					'pronamic_twinfield_offices',
					\__( 'Offices', 'twinfield' ),
					[ $this, 'meta_box_offices' ],
					$post_type,
					'normal',
					'high'
				);
			}
		}

		\add_meta_box(
			'pronamic_twinfield_authorize',
			\__( 'Authorize', 'twinfield' ),
			[ $this, 'meta_box_authorize' ],
			$post_type,
			'normal',
			'high'
		);

		\add_meta_box(
			'pronamic_twinfield_menu',
			\__( 'Menu', 'twinfield' ),
			[ $this, 'meta_box_menu' ],
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
