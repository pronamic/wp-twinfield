<?php
/**
 * Meta Box Offices
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

$client = $plugin->get_client( $post );

$offices = $client->get_offices();

$i = 1;

?>
<table>
	<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Code</th>
			<th scope="col">Name</th>
			<th scope="col">Shortname</th>
		</tr>
	</thead>

	<tbody>

		<?php foreach ( $offices as $office ) : ?>

			<tr>
				<td>
					<?php echo \esc_html( $i++ ); ?>
				</td>
				<td>
					<code><?php echo \esc_html( $office->get_code() ); ?></code>
				</td>
				<td>
					<?php echo \esc_html( $office->get_name() ); ?>
				</td>
				<td>
					<?php echo \esc_html( $office->get_shortname() ); ?>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>
