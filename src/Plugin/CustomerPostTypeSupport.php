<?php
/**
 * Customer Post Type Support
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;

/**
 * Customer Post Type Support class
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class CustomerPostTypeSupport {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ], 10, 2 );

		\add_action( 'save_post', [ $this, 'save_post' ] );
	}

	/**
	 * Save post.
	 * 
	 * @param int $post_id WordPress post ID.
	 * @return void
	 */
	public function save_post( $post_id ) {
		$post_type = \get_post_type( $post_id );

		if ( false === $post_type ) {
			return;
		}

		if ( ! \post_type_supports( $post_type, 'twinfield_customer' ) ) {
			return;
		}

		if ( ! \array_key_exists( 'pronamic_twinfield_customer_post_nonce', $_POST ) ) {
			return;
		}

		if ( ! \wp_verify_nonce( \sanitize_key( $_POST['pronamic_twinfield_customer_post_nonce'] ), 'pronamic_twinfield_customer_post_save' ) ) {
			return;
		}

		if ( \array_key_exists( 'pronamic_twinfield_customer_id', $_POST ) ) {
			$customer_id = \sanitize_text_field( \wp_unslash( $_POST['pronamic_twinfield_customer_id'] ) );

			if ( empty( $customer_id ) ) {
				\delete_post_meta( $post_id, '_twinfield_customer_id' );
			} else {
				\update_post_meta( $post_id, '_twinfield_customer_id', $customer_id );
			}
		}
	}

	/**
	 * Add meta boxes.
	 * 
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Post object.
	 */
	public function add_meta_boxes( $post_type, $post ) {
		if ( ! \post_type_supports( $post_type, 'twinfield_customer' ) ) {
			return;
		}

		\add_meta_box(
			'pronamic_twinfield_customer',
			\__( 'Twinfield Customer', 'twinfield' ),
			[ $this, 'meta_box_customer' ],
			$post_type,
			'normal',
			'default'
		);
	}

	/**
	 * Meta box customer.
	 * 
	 * @link https://github.com/WordPress/WordPress/blob/5.8/wp-admin/includes/template.php#L1395
	 * @param WP_Post $post Post.
	 * @param array   $box  Box.
	 */
	public function meta_box_customer( $post, $box ) {
		\wp_nonce_field( 'pronamic_twinfield_customer_post_save', 'pronamic_twinfield_customer_post_nonce' );

		include __DIR__ . '/../../admin/meta-box-customer.php';
	}
}
