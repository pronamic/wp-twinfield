<?php
/**
 * Meta box save schedule
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

$fields = [
	[
		'meta_key' => '_pronamic_twinfield_save_offices_schedule',
		'label'    => \__( 'Offices', 'pronamic-twinfield' ),
	],
	[
		'meta_key' => '_pronamic_twinfield_save_hierarchies_schedule',
		'label'    => \__( 'Hierarchies', 'pronamic-twinfield' ),
	],
];

?>
<table>
	<thead>
		<tr>
			<th scope="col"><?php \esc_html_e( 'Entity', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php \esc_html_e( 'Schedule', 'pronamic-twinfield' ); ?></th>
		</tr>
	</thead>

	<tbody>

		<?php foreach ( $fields as $field ) : ?>

			<tr>
				<th scope="row"><?php echo \esc_html( $field['label'] ); ?></th>
				<td>
					<?php

					$name  = $field['meta_key'];
					$value = \get_post_meta( $post->ID, $name, true );

					printf(
						'<input type="text" name="%s" value="%s" />',
						\esc_attr( $name ),
						\esc_attr( $value ),
					);

					?>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>
