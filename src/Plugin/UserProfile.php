<?php
/**
 * User profile
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use WP_User;

/**
 * User profile class
 *
 * Adds a personal "Default Authorization" setting to the user profile.
 */
class UserProfile {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'show_user_profile', $this->render( ... ) );
		\add_action( 'edit_user_profile', $this->render( ... ) );

		\add_action( 'personal_options_update', $this->save( ... ) );
		\add_action( 'edit_user_profile_update', $this->save( ... ) );
	}

	/**
	 * Render user profile fields.
	 *
	 * @param WP_User $user User.
	 * @return void
	 */
	public function render( $user ) {
		if ( ! \current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}

		$selected = \get_user_meta( $user->ID, 'pronamic_twinfield_authorization_post_id', true );

		$global_post_id = (int) \get_option( 'pronamic_twinfield_authorization_post_id' );

		?>
		<h2><?php \esc_html_e( 'Twinfield', 'pronamic-twinfield' ); ?></h2>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row">
					<label for="pronamic_twinfield_authorization_post_id"><?php \esc_html_e( 'Default Authorization', 'pronamic-twinfield' ); ?></label>
				</th>
				<td>
					<?php

					\wp_dropdown_pages(
						[
							'name'              => 'pronamic_twinfield_authorization_post_id',
							'id'                => 'pronamic_twinfield_authorization_post_id',
							'post_type'         => 'pronamic_twf_auth',
							'selected'          => (int) $selected,
							'show_option_none'  => \esc_html__( '— Use global default —', 'pronamic-twinfield' ),
							'option_none_value' => '',
							'class'             => 'regular-text',
						]
					);

					?>
					<p class="description">
						<?php

						if ( $global_post_id > 0 ) {
							\printf(
								/* translators: %s: authorization title */
								\esc_html__( 'Global default: %s', 'pronamic-twinfield' ),
								\esc_html( \get_the_title( $global_post_id ) )
							);
						} else {
							\esc_html_e( 'No global default authorization configured.', 'pronamic-twinfield' );
						}

						?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save user profile fields.
	 *
	 * WordPress core verifies the `update-user_{$user_id}` nonce before these hooks fire.
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function save( $user_id ) {
		if ( ! \current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( ! \array_key_exists( 'pronamic_twinfield_authorization_post_id', $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified by WordPress core.
			return;
		}

		$post_id = \absint( \wp_unslash( $_POST['pronamic_twinfield_authorization_post_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified by WordPress core.

		if ( 0 === $post_id || 'pronamic_twf_auth' !== \get_post_type( $post_id ) ) {
			\delete_user_meta( $user_id, 'pronamic_twinfield_authorization_post_id' );

			return;
		}

		\update_user_meta( $user_id, 'pronamic_twinfield_authorization_post_id', $post_id );
	}
}
