<?php
/**
 * Twinfield sales invoice template.
 *
 * @link https://github.com/wp-twinfield/wp-twinfield/blob/develop/templates/sales-invoice.php
 * @package Pronamic/WordPress/Twinfield
 */

use Pronamic\WordPress\Money\Money;

get_header();

?>
<div id="container">
	<div id="content" role="main">
		<div class="page-header d-flex justify-content-between">
			<?php

			printf(
				'<h1>%s</h1>',
				sprintf(
					'Sales Invoice %s - %s - %s',
					esc_html( $sales_invoice->get_header()->get_office() ),
					esc_html( $sales_invoice->get_header()->get_type() ),
					esc_html( $sales_invoice->get_header()->get_number() )
				)
			);

			?>

			<div class="btn-group" role="group" aria-label="Basic example">
				<a class="btn btn-secondary" href="<?php echo esc_url( untrailingslashit( add_query_arg() ) . '.pdf' ); ?>" target="_blank">
					<i class="fas fa-file-pdf"></i> PDF
				</a>

				<a class="btn btn-secondary" href="<?php echo esc_url( untrailingslashit( add_query_arg() ) . '.xml' ); ?>" target="_blank">
					<i class="fas fa-file-code"></i> XML
				</a>

				<a class="btn btn-secondary" href="<?php echo esc_url( rest_url( $request->get_route() ) ); ?>" target="_blank">
					<i class="fas fa-file-code"></i> JSON
				</a>
			</div>
		</div>

		<?php

		$items = [
			(object) [
				'label' => __( 'Office', 'pronamic-twinfield' ),
				'value' => $sales_invoice->get_header()->get_office(),
			],
			(object) [
				'label' => __( 'Type', 'pronamic-twinfield' ),
				'value' => $sales_invoice->get_header()->get_type(),
			],
			(object) [
				'label' => __( 'Number', 'pronamic-twinfield' ),
				'value' => $sales_invoice->get_header()->get_number(),
			],
			(object) [
				'label' => __( 'Invoice Date', 'pronamic-twinfield' ),
				'value' => $sales_invoice->get_header()->get_date()->format( 'd-m-Y' ),
			],
			(object) [
				'label' => __( 'Due Date', 'pronamic-twinfield' ),
				'value' => $sales_invoice->get_header()->get_due_date()->format( 'd-m-Y' ),
			],
			(object) [
				'label' => __( 'Customer', 'pronamic-twinfield' ),
				'value' => $sales_invoice->get_header()->get_customer(),
			],
			(object) [
				'label' => __( 'Status', 'pronamic-twinfield' ),
				'value' => $sales_invoice->get_header()->get_status(),
			],
		];

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

		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Lines', 'pronamic-twinfield' ); ?></h3>
			</header>

			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'ID', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Article', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Sub Article', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Quantity', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Description', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Value Excl', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Vat Value', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Value Inc', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Free Text 1', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Free Text 2', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Free Text 3', 'pronamic-twinfield' ); ?></th>
					</tr>
				</thead>

				<tbody>

					<?php foreach ( $sales_invoice->get_lines() as $line ) : ?>

						<tr>
							<td>
								<?php echo esc_html( $line->get_id() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_article() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_subarticle() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_quantity() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_description() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_value_excl() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_vat_value() ); ?>
							</td>
							<td>
								<?php echo esc_html( $line->get_value_inc() ); ?>
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

		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Totals', 'pronamic-twinfield' ); ?></h3>
			</header>

			<div class="content">
				<dl class="dl-horizontal">
					<dt><?php esc_html_e( 'Value Excl', 'twinfield' ); ?></dt>
					<dd><?php echo esc_html( ( new Money( $sales_invoice->get_value_excl() ) )->format_i18n() ); ?></dd>

					<dt><?php esc_html_e( 'Vat Value', 'twinfield' ); ?></dt>
					<dd><?php echo esc_html( ( new Money( $sales_invoice->get_vat_value() ) )->format_i18n() ); ?></dd>

					<dt><?php esc_html_e( 'Value Inc', 'twinfield' ); ?></dt>
					<dd><?php echo esc_html( ( new Money( $sales_invoice->get_value_inc() ) )->format_i18n() ); ?></dd>
				</dl>
			</div>
		</div>

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
