<?php

get_header();

printf(
	'<h1>%s</h1>',
	\sprintf(
		'Sales Invoice %s - %s - %s',
		$sales_invoice->header->office->get_code(),
		$sales_invoice->header->invoice_type->code,
		$data->invoice_number,
	)
);

$items = array(
	(object) array(
		'label' => __( 'Office', 'pronamic-twinfield' ),
		'value' => $sales_invoice->header->office->get_code(),
	),
	(object) array(
		'label' => __( 'Type', 'pronamic-twinfield' ),
		'value' => $sales_invoice->header->invoice_type->code,
	),
	(object) array(
		'label' => __( 'Number', 'pronamic-twinfield' ),
		'value' => $data->invoice_number,
	),
	(object) array(
		'label' => __( 'Invoice Date', 'pronamic-twinfield' ),
		'value' => $sales_invoice->header->invoice_date->format( 'd-m-Y' ),
	),
	(object) array(
		'label' => __( 'Due Date', 'pronamic-twinfield' ),
		'value' => $sales_invoice->header->due_date->format( 'd-m-Y' ),
	),
	(object) array(
		'label' => __( 'Customer', 'pronamic-twinfield' ),
		'value' => $sales_invoice->header->customer,
	),
	(object) array(
		'label' => __( 'Period', 'pronamic-twinfield' ),
		'value' => $sales_invoice->header->period,
	),
	(object) array(
		'label' => __( 'Status', 'pronamic-twinfield' ),
		'value' => $sales_invoice->header->status,
	),
);

?>

<h2><?php esc_html_e( 'Header', 'pronamic-twinfield' ); ?></h2>

<dl>
	<?php foreach ( $items as $item ) : ?>

		<dt><?php echo esc_html( $item->label ); ?></dt>
		<dd><?php echo esc_html( $item->value ); ?></dd>

	<?php endforeach; ?>

</dl>

<h2><?php esc_html_e( 'Lines', 'pronamic-twinfield' ); ?></h2>

<table>
	<thead>
		<tr>
			<th scope="col"><?php esc_html_e( 'ID', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Article', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Sub Article', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Quantity', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Description', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Value excl', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Free Text 1', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Free Text 2', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Free Text 3', 'pronamic-twinfield' ); ?></th>
		</tr>
	</thead>

	<tbody>
		
		<?php foreach ( $sales_invoice->lines as $line ) : ?>

			<tr>
				<td>
					<?php echo esc_html( $line->id ); ?>
				</td>
				<td>
					<?php echo esc_html( $line->article ); ?>
				</td>
				<td>
					<?php echo esc_html( $line->subarticle ); ?>
				</td>
				<td>
					<?php echo esc_html( $line->quantity ); ?>
				</td>
				<td>
					<?php echo esc_html( $line->description ); ?>
				</td>
				<td>
					<?php echo esc_html( $line->value_excl ); ?>
				</td>
				<td>
					<?php echo esc_html( $line->free_text_1 ); ?>
				</td>
				<td>
					<?php echo esc_html( $line->free_text_2 ); ?>
				</td>
				<td>
					<?php echo esc_html( $line->free_text_3 ); ?>
				</td>
			</tr>

		<?php endforeach; ?>

	</tbody>
</table>

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

foreach ( $xml_sections as $section ) {
	printf(
		'<h2>%s</h2>',
		\esc_html( $section->label )
	);

	$document = new \DOMDocument();

	$document->preserveWhiteSpace = false;
	$document->formatOutput       = true;

	$document->loadXML( $section->xml );

	\printf(
		'<pre>%s</pre>',
		\esc_html( $document->saveXML( $document->documentElement ) )
	);
}

get_footer();
