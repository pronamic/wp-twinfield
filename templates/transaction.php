<?php
/**
 * Twinfield sales invoice template.
 *
 * @link https://github.com/wp-twinfield/wp-twinfield/blob/develop/templates/sales-invoice.php
 * @package Pronamic/WordPress/Twinfield
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
					'Transaction %s - %s - %s',
					esc_html( $transaction->get_office()->get_code() ),
					esc_html( $transaction->get_type()->get_code() ),
					esc_html( $transaction->get_number() )
				)
			);

			?>
		</div>

		<?php

		$items = [
			(object) [
				'label' => __( 'Number', 'pronamic-twinfield' ),
				'value' => $transaction->get_number(),
			],
			(object) [
				'label' => __( 'Date', 'pronamic-twinfield' ),
				'value' => $transaction->get_header()->get_date()->format( 'd-m-Y' ),
			],
		];

		?>
		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Header', 'pronamic-twinfield' ); ?></h3>
			</header>

			<div class="content">
				<dl class="dl-horizontal">
					<dt><?php esc_html_e( 'Office', 'pronamic-twinfield' ); ?></dt>
					<dd>
						<?php

						$office = $transaction->get_office();

						printf(
							'<a href="%s">%s</a>',
							\esc_url( $this->get_link( $post_id, $office ) ),
							\esc_html( $office->get_code() )
						);

						?>
					</dd>

					<dt><?php esc_html_e( 'Code', 'pronamic-twinfield' ); ?></dt>
					<dd>
						<?php

						$transaction_type = $transaction->get_type();

						printf(
							'<a href="%s">%s</a>',
							\esc_url( $this->get_link( $post_id, $transaction_type ) ),
							\esc_html( $transaction_type->get_code() )
						);

						?>
					</dd>

					<?php foreach ( $items as $item ) : ?>

						<dt><?php echo esc_html( $item->label ); ?></dt>
						<dd><?php echo esc_html( $item->value ); ?></dd>

					<?php endforeach; ?>

				</dl>
			</div>
		</div>

		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Lines', 'pronamic-twinfield' ); ?></h3>
			</header>

			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'ID', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Dimension 1', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Dimension 2', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Dimension 3', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Description', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Value', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Value Open', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Free Text 1', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Free Text 2', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Free Text 3', 'pronamic-twinfield' ); ?></th>
					</tr>
				</thead>

				<tbody>

					<?php foreach ( $transaction->get_lines() as $line ) : ?>

						<tr>
							<td>
								<?php echo esc_html( $line->get_id() ); ?>
							</td>

							<?php

							$dimensions = [
								$line->get_dimension_1(),
								$line->get_dimension_2(),
								$line->get_dimension_3(),
							];

							foreach ( $dimensions as $dimension ) : 
								?>

								<td>
									<?php

									if ( null !== $dimension ) {
										printf(
											'<a href="%s">%s</a>',
											\esc_url( $this->get_link( $post_id, $dimension ) ),
											\esc_html( 
												\sprintf(
													'%s - %s - %s',
													$dimension->get_type()->get_code(),
													$dimension->get_code(),
													$dimension->get_name()
												)
											)
										);
									}

									?>
								</td>

							<?php endforeach; ?>

							<td>
								<?php echo esc_html( $line->get_description() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_base_value() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_base_value_open() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_free_text_1() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_free_text_2() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_free_text_3() ); ?>
							</td>
						</tr>

					<?php endforeach; ?>

				</tbody>
			</table>
		</div>

		<?php

		$xml_sections = [];

		if ( $data->_embedded->request ) {
			$xml_sections[] = (object) [
				'label' => __( 'Request XML', 'pronamic-twinfield' ),
				'xml'   => $data->_embedded->request,
			];
		}

		if ( $data->_embedded->response ) {
			$xml_sections[] = (object) [
				'label' => __( 'Response XML', 'pronamic-twinfield' ),
				'xml'   => $data->_embedded->response,
			];
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

		<?php

		$xml_sections = [];

		if ( $data->_embedded->request_xml ) {
			$xml_sections[] = (object) [
				'label' => __( 'Request XML', 'pronamic-twinfield' ),
				'xml'   => $data->_embedded->request_xml,
			];
		}

		if ( $data->_embedded->response_xml ) {
			$xml_sections[] = (object) [
				'label' => __( 'Response XML', 'pronamic-twinfield' ),
				'xml'   => $data->_embedded->response_xml,
			];
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
