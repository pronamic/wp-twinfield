<?php
/**
 * Meta Box Authentication
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

$client = $plugin->get_client( $post );

$authentication = $client->get_authentication();

$validation = $authentication->get_validation();

?>
<table class="form-table">
	<tr>
		<th scope="row"><?php \esc_html_e( 'Organisation', 'pronamic-twinfield' ); ?></th>
		<td><?php echo \esc_html( $validation->organisation->get_code() ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php \esc_html_e( 'User', 'pronamic-twinfield' ); ?></th>
		<td><?php echo \esc_html( $validation->user->get_code() ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php \esc_html_e( 'Expiration', 'pronamic-twinfield' ); ?></th>
		<td><?php echo \esc_html( \wp_date( 'd-m-Y H:i:s', $validation->get_expiration_datetime()->getTimestamp() ) ); ?></td>
	</tr>
</table>
