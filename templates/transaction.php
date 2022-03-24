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

		$items = array(
			(object) array(
				'label' => __( 'Office', 'pronamic-twinfield' ),
				'value' => $transaction->get_office()->get_code(),
			),
			(object) array(
				'label' => __( 'Code', 'pronamic-twinfield' ),
				'value' => $transaction->get_type()->get_code(),
			),
			(object) array(
				'label' => __( 'Number', 'pronamic-twinfield' ),
				'value' => $transaction->get_number(),
			),
			(object) array(
				'label' => __( 'Date', 'pronamic-twinfield' ),
				'value' => $transaction->get_header()->get_date()->format( 'd-m-Y' ),
			),
		);

		?>
		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Header', 'pronamic-twinfield' ); ?></h3>
			</header>

			<div class="content">
				<dl class="dl-horizontal">

					<?php foreach ( $items as $item ) : ?>

						<dt><?php echo esc_html( $item->label ); ?></dt>
						<dd><?php echo esc_html( $item->value ); ?></dd>

					<?php endforeach; ?>

				</dl>
			</div>
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

		<?php

		$xml_sections = array();

		if ( $data->_embedded->request_xml ) {
			$xml_sections[] = (object) array(
				'label' => __( 'Request XML', 'pronamic-twinfield' ),
				'xml'   => $data->_embedded->request_xml,
			);
		}

		if ( $data->_embedded->response_xml ) {
			$xml_sections[] = (object) array(
				'label' => __( 'Response XML', 'pronamic-twinfield' ),
				'xml'   => $data->_embedded->response_xml,
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
