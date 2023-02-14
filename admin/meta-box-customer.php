<?php
/**
 * Meta Box Customer
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @package Pronamic/WordPress/Twinfield
 */

$customer_id = get_post_meta( $post->ID, '_twinfield_customer_id', true );

?>
<table class="form-table">
	<tr>
		<th scope="row">
			<label for="pronamic_twinfield_customer_id"><?php esc_html_e( 'Customer ID', 'pronamic-twinfield' ); ?></label>
		</th>
		<td>
			<input id="pronamic_twinfield_customer_id" type="text" name="pronamic_twinfield_customer_id" value="<?php echo esc_attr( $customer_id ); ?>" />
		</td>
	</tr>
</table>
