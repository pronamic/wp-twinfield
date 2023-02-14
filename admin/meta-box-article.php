<?php
/**
 * Meta Box Article
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @package Pronamic/WordPress/Twinfield
 */

$article_code    = get_post_meta( $post->ID, '_twinfield_article_code', true );
$subarticle_code = get_post_meta( $post->ID, '_twinfield_subarticle_code', true );

?>
<table class="form-table">
	<tr>
		<th scope="row">
			<label for="twinfield_article_code"><?php esc_html_e( 'Article Code', 'pronamic-twinfield' ); ?></label>
		</th>
		<td>
			<input type="text" id="pronamic_twinfield_article_code" name="pronamic_twinfield_article_code" value="<?php echo esc_attr( $article_code ); ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="pronamic_twinfield_subarticle_code"><?php esc_html_e( 'Subarticle Code', 'pronamic-twinfield' ); ?></label>
		</th>
		<td>
			<input type="text" id="pronamic_twinfield_subarticle_code" name="pronamic_twinfield_subarticle_code" value="<?php echo esc_attr( $subarticle_code ); ?>" />
		</td>
	</tr>
</table>
