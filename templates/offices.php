<?php

get_header();

?>
<table>
	<thead>
		<tr>
			<th scope="col"><?php esc_html_e( 'Code', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Name', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Shortname', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Page URL', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Rest URL', 'pronamic-twinfield' ); ?></th>
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
				<td>
					<?php

					$url = home_url( 
						strtr(
							'pronamic-twinfield/authorizations/:auth_post_id/offices/:office_code',
							[
								':auth_post_id' => $request->get_param( 'post_id' ),
								':office_code'  => $office->get_code(),
							]
						)
					);

					printf(
						'<a href="%s">%s</a>',
						esc_url( $url ),
						esc_html( $url )
					);

					?>
				</td>
				<td>
					<?php

					$url = rest_url( 
						strtr(
							'pronamic-twinfield/v1/authorizations/:auth_post_id/offices/:office_code',
							[
								':auth_post_id' => $request->get_param( 'post_id' ),
								':office_code'  => $office->get_code(),
							]
						)
					);

					printf(
						'<a href="%s">%s</a>',
						esc_url( $url ),
						esc_html( $url )
					);

					?>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>

<?php

get_footer();
