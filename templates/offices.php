<?php

get_header();

?>
<table>
	<thead>
		<tr>
			<th scope="col"><?php esc_html_e( 'Code', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Name', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Shortname', 'pronamic-twinfield' ); ?></th>
		</tr>
	</thead>

	<tbody>
		
		<?php foreach ( $offices as $office ) : ?>

			<tr>
				<td>
					<code><?php echo esc_html( $office->get_code() ); ?></code>
				</td>
				<td>
					<?php echo esc_html( $office->get_name() ); ?>
				</td>
				<td>
					<?php echo esc_html( $office->get_shortname() ); ?>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>

<?php

get_footer();
