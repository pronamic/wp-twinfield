<?php
/**
 * Article Post Type Support
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use Pronamic\WordPress\Twinfield\Authentication\AuthenticationInfo;
use Pronamic\WordPress\Twinfield\Authentication\OpenIdConnectClient;

/**
 * Article Post Type Support class
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class ArticlePostTypeSupport {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'add_meta_boxes', $this->add_meta_boxes( ... ) );

		\add_action( 'save_post', $this->save_post( ... ) );
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

		if ( ! \post_type_supports( $post_type, 'twinfield_article' ) ) {
			return;
		}

		if ( ! \array_key_exists( 'pronamic_twinfield_article_post_nonce', $_POST ) ) {
			return;
		}

		if ( ! \wp_verify_nonce( \sanitize_key( $_POST['pronamic_twinfield_article_post_nonce'] ), 'pronamic_twinfield_article_post_save' ) ) {
			return;
		}

		$map = [
			'pronamic_twinfield_article_code'    => '_twinfield_article_code',
			'pronamic_twinfield_subarticle_code' => '_twinfield_subarticle_code',
		];

		foreach ( $map as $key => $meta_key ) {
			if ( \array_key_exists( $key, $_POST ) ) {
				$meta_value = \sanitize_text_field( \wp_unslash( $_POST[ $key ] ) );

				if ( empty( $meta_value ) ) {
					\delete_post_meta( $post_id, $meta_key );
				} else {
					\update_post_meta( $post_id, $meta_key, $meta_value );
				}
			}
		}
	}

	/**
	 * Add meta boxes.
	 * 
	 * @param string $post_type Post type.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( ! \post_type_supports( $post_type, 'twinfield_article' ) ) {
			return;
		}

		\add_meta_box(
			'pronamic_twinfield_article',
			\__( 'Twinfield Article', 'twinfield' ),
			$this->meta_box_article( ... ),
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
	public function meta_box_article( $post, $box ) {
		\wp_nonce_field( 'pronamic_twinfield_article_post_save', 'pronamic_twinfield_article_post_nonce' );

		include __DIR__ . '/../../admin/meta-box-article.php';
	}
}
