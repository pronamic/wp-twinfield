<?php

namespace Pronamic\WordPress\Twinfield;

$items = [
	[
		'label' => \__( 'Organisation', 'pronamic-twinfield' ),
		'path'  => 'organisation',
	],
	[
		'label' => \__( 'Offices', 'pronamic-twinfield' ),
		'path'  => 'offices',
	],
	[
		'label' => \__( 'Finder Types', 'pronamic-twinfield' ),
		'path'  => 'finder-types',
	],
];

?>
<table>
	<thead>
		<tr>
			<th scope="col"><?php \esc_html_e( 'Item', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php \esc_html_e( 'Page URL', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php \esc_html_e( 'REST URL', 'pronamic-twinfield' ); ?></th>
		</tr>
	</thead>

	<tbody>

		<?php foreach ( $items as $item ) : ?>

			<tr>
				<td>
					<?php echo \esc_html( $item['label'] ); ?>
				</td>
				<td>
					<?php

					$url = \home_url( 'pronamic-twinfield/authorizations/' . $post->ID . '/' . $item['path'] );

					\printf(
						'<a href="%s">%s</a>',
						\esc_url( $url ),
						\esc_html( $url )
					);

					?>
				</td>
				<td>
					<?php

					$url = \rest_url( 'pronamic-twinfield/v1/authorizations/' . $post->ID . '/' . $item['path'] );

					\printf(
						'<a href="%s">%s</a>',
						\esc_url( $url ),
						\esc_html( $url )
					);

					?>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>
