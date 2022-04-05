<?php
/**
 * Twinfield dimension template.
 *
 * @package Pronamic/WordPress/Twinfield
 */

get_header();

$office = $dimension->get_office();

$organisation = $office->get_organisation();

$twinfield = $organisation->get_twinfield();

?>

<div id="container">
	<div id="content" role="main">
		<div class="page-header d-flex justify-content-between">
			<?php

			printf(
				'<h1>%s</h1>',
				sprintf(
					'Dimension %s - %s - %s - %s',
					esc_html( $organisation->get_code() ),
					esc_html( $office->get_code() ),
					esc_html( $dimension->get_type() ),
					esc_html( $dimension->get_code() )
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

			<h2><?php esc_html_e( 'Details', 'pronamic-twinfield' ); ?></h2>

			<dl>
				<dt><?php esc_html_e( 'Organisation', 'pronamic-twinfield' ); ?></dt>
				<dd>
					<?php

					$url = home_url( 
						strtr(
							'pronamic-twinfield/authorizations/:auth_post_id/organisation',
							[
								':auth_post_id' => $request->get_param( 'post_id' ),
							]
						)
					);

					printf(
						'<a href="%s"><code>%s</code></a>',
						esc_url( $url ),
						esc_html( $organisation->get_code() )
					);

					?>
				</dd>

				<dt><?php esc_html_e( 'Office', 'pronamic-twinfield' ); ?></dt>
				<dd>
					<?php

					$url = home_url( 
						strtr(
							'pronamic-twinfield/authorizations/:auth_post_id/organisation',
							[
								':auth_post_id' => $request->get_param( 'post_id' ),
							]
						)
					);

					printf(
						'<a href="%s"><code>%s</code></a>',
						esc_url( $url ),
						esc_html( $office->get_code() )
					);

					?>
				</dd>

				<dt><?php esc_html_e( 'Type', 'pronamic-twinfield' ); ?></dt>
				<dd><code><?php echo esc_html( $dimension->get_type() ); ?></code></dd>

				<dt><?php esc_html_e( 'Code', 'pronamic-twinfield' ); ?></dt>
				<dd><code><?php echo esc_html( $dimension->get_code() ); ?></code></dd>

				<dt><?php esc_html_e( 'Name', 'pronamic-twinfield' ); ?></dt>
				<dd><?php echo esc_html( $dimension->get_name() ); ?></dd>

				<dt><?php esc_html_e( 'Shortname', 'pronamic-twinfield' ); ?></dt>
				<dd><?php echo esc_html( $dimension->get_shortname() ); ?></dd>
			</dl>
		</div>

		<?php

		$xml_sections = array();

		if ( $data->_embedded->request ) {
			$xml_sections[] = (object) array(
				'label' => __( 'Request XML', 'pronamic-twinfield' ),
				'xml'   => $data->_embedded->request,
			);
		}

		if ( $data->_embedded->response ) {
			$xml_sections[] = (object) array(
				'label' => __( 'Response XML', 'pronamic-twinfield' ),
				'xml'   => $data->_embedded->response,
			);
		}

		foreach ( $xml_sections as $section ) :
			?>

			<div class="panel">
				<header>
					<?php

					printf(
						'<h3>%s</h3>',
						esc_html( $section->label )
					);

					?>
				</header>

				<?php

				$document = new DOMDocument();

				$document->preserveWhiteSpace = false;
				$document->formatOutput       = true;

				$document->loadXML( $section->xml );

				printf(
					'<textarea class="pronamic-twinfield-xml-textarea">%s</textarea>',
					esc_html( $document->saveXML( $document->documentElement ) )
				);

				?>
			</div>

		<?php endforeach; ?>
	</div>
</div>

<?php

require __DIR__ . '/scripts.php';

get_footer();
