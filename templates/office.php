<?php
/**
 * Twinfield sales invoice template.
 *
 * @link https://github.com/wp-twinfield/wp-twinfield/blob/develop/templates/sales-invoice.php
 */

get_header();

?>

<div id="container">
	<div id="content" role="main">
		<div class="page-header d-flex justify-content-between">
			<?php

			printf(
				'<h1>%s</h1>',
				sprintf(
					'Office %s - %s',
					$office->organisation->get_code(),
					$office->get_code()
				)
			);

			?>

			<div class="btn-group" role="group" aria-label="Basic example">
				<a class="btn btn-secondary" href="<?php echo esc_url( untrailingslashit( add_query_arg() ) . '.xml' ); ?>" target="_blank">
					<i class="fas fa-file-code"></i> XML
				</a>

                <a class="btn btn-secondary" href="<?php echo esc_url( rest_url( $request->get_route() ) ); ?>" target="_blank">
                    <i class="fas fa-file-code"></i> JSON
                </a>
			</div>
		</div>
	</div>
</div>

<?php

get_footer();
