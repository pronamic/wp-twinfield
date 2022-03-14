<?php
/**
 * Twinfield example.
 *
 * @link https://github.com/wp-twinfield/wp-twinfield/blob/develop/templates/sales-invoice.php
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

$autoload_file = __DIR__ . '/../vendor/autoload.php';

if ( ! is_readable( $autoload_file ) ) {
	die( 'Run `composer install`.' );
}

require __DIR__ . '/../vendor/autoload.php';

// WorDBless.
\WorDBless\Load::load();

// Xdebug.

// phpcs:ignore WordPress.PHP.IniSet.Risky
\ini_set( 'xdebug.var_display_max_depth', -1 );
// phpcs:ignore WordPress.PHP.IniSet.Risky
\ini_set( 'xdebug.var_display_max_children', -1 );
// phpcs:ignore WordPress.PHP.IniSet.Risky
\ini_set( 'xdebug.var_display_max_data', -1 );

// Load.
$file = __DIR__ . '/client-secret.json';

if ( ! is_readable( $file ) ) {
	die( 'Create `client-secret.json` file.' );
}

$openid_connect_client = Authentication\OpenIdConnectClient::from_json_file( $file );

// Authentication.
$authentication_file = __DIR__ . '/authentication-secret.json';

if ( \is_readable( $authentication_file ) ) {
	$authentication = Authentication\AuthenticationInfo::from_object( \json_decode( \file_get_contents( $authentication_file, true ) ) );
}

/**
 * @link https://github.com/googleapis/google-api-php-client/blob/master/docs/oauth-web.md#create-authorization-credentials
 * @link https://developers.google.com/gmail/api/quickstart/php
 */
if ( \array_key_exists( 'code', $_GET ) ) {
	$response = $openid_connect_client->get_access_token( $_GET['code'] );

	$tokens = Authentication\AuthenticationTokens::from_object( $response );

	$response = $openid_connect_client->get_access_token_validation( $tokens->get_access_token() );

	$validation = Authentication\AccessTokenValidation::from_object( $response );

	$authentication = new Authentication\AuthenticationInfo( $tokens, $validation );

	\file_put_contents( $authentication_file, \wp_json_encode( $authentication, \JSON_PRETTY_PRINT ) );

	$url = \remove_query_arg( 'code' );

	\wp_safe_redirect( $url );

	exit;
}

if ( isset( $authentication ) ) {
	$client = new Client( $openid_connect_client, $authentication );

	$client->set_authentication_refresh_handler(
		function( $client ) use ( $authentication_file ) {
			\file_put_contents( $authentication_file, \wp_json_encode( $client->get_authentication(), \JSON_PRETTY_PRINT ) );
		} 
	);

	$organisation = $client->get_organisation();

	if ( \array_key_exists( 'pronamic_twinfield_process_xml', $_POST ) ) {
		$office_code = \wp_unslash( $_POST['pronamic_twinfield_office_code'] );

		$office = $organisation->new_office( $office_code );

		$xml = \wp_unslash( $_POST['pronamic_twinfield_xml'] );

		$process_xml_string = new ProcessXmlString( $xml );

		$xml_processor = $client->get_xml_processor();

		$xml_processor->set_office( $office );

		$xml_response = $xml_processor->process_xml_string( $process_xml_string );

		$transaction_response = Transactions\TransactionResponse::from_xml( $xml_response->get_result() );

		var_dump( $transaction_response );
	}
}

?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<title>Twinfield Examples</title>

		<link rel="stylesheet" type="text/css" href="https://unpkg.com/codemirror@5.62.2/lib/codemirror.css" />
	</head>

	<body>
		<h1>Twinfield Examples</h1>

		<p>
			<a href="https://developers.twinfield.com/">https://developers.twinfield.com/</a>
		</p>

		<?php if ( isset( $client ) ) : ?>

			<h2>Organisation</h2>

			<?php

			$organisation = $client->get_organisation();

			?>

			<dl>
				<dt>Code</dt>
				<dd><code><?php echo \esc_html( $organisation->get_code() ); ?></code></dd>

				<dt>UUID</dt>
				<dd><code><?php echo \esc_html( (string) $organisation->get_uuid() ); ?></code></dd>
			</dl>

			<h2>User</h2>

			<?php

			$user = $client->get_user();

			?>

			<dl>
				<dt>Code</dt>
				<dd><code><?php echo \esc_html( $user->get_code() ); ?></code></dd>
			</dl>

			<h2>Offices</h2>

			<?php

			$offices = $client->get_offices();

			?>
			<table>
				<thead>
					<tr>
						<th scope="col">Code</th>
						<th scope="col">Name</th>
						<th scope="col">Shortname</th>
					</tr>
				</thead>

				<tbody>
					
					<?php foreach ( $offices as $office ) : ?>

						<tr>
							<td>
								<code><?php echo \esc_html( $office->get_code() ); ?></code>
							</td>
							<td>
								<?php echo \esc_html( $office->get_name() ); ?>
							</td>
							<td>
								<?php echo \esc_html( $office->get_shortname() ); ?>
							</td>
						</tr>

					<?php endforeach; ?>

				</tbody>
			</table>

			<?php

			$office = \reset( $offices );

			if ( false !== $office ) : 
				?>

				<?php

				$office = $client->get_office( $office );

				?>
				<h2>Office</h2>

				<dl>
					<dt>Code</dt>
					<dd><code><?php echo \esc_html( $office->get_code() ); ?></code></dd>

					<dt>Name</dt>
					<dd><?php echo \esc_html( (string) $office->get_name() ); ?></dd>

					<dt>Shortname</dt>
					<dd><?php echo \esc_html( (string) $office->get_shortname() ); ?></dd>
				</dl>
				
				<h2>Transaction Types</h2>

				<?php

				$transaction_types = $client->get_transaction_types( $office );

				?>
				<table>
					<thead>
						<tr>
							<th scope="col">Code</th>
							<th scope="col">Name</th>
						</tr>
					</thead>

					<tbody>
						
						<?php foreach ( $transaction_types as $transaction_type ) : ?>

							<tr>
								<td>
									<code><?php echo \esc_html( $transaction_type->get_code() ); ?></code>
								</td>
								<td>
									<?php echo \esc_html( $transaction_type->get_name() ); ?>
								</td>
							</tr>

						<?php endforeach; ?>

					</tbody>
				</table>
				
				<h2>Transaction Type</h2>

				<?php

				$transaction_type = $office->new_transaction_type( 'MEMO' );

				?>

				<dl>
					<dt>Code</dt>
					<dd><code><?php echo \esc_html( $transaction_type->get_code() ); ?></code></dd>
				</dl>
				
				<h2>Transaction</h2>

				<?php

				$transaction = $transaction_type->new_transaction();

				$dimension_type_pnl = $office->new_dimension_type( 'PNL' );
				$dimension_type_crd = $office->new_dimension_type( 'CRD' );
				$dimension_type_bas = $office->new_dimension_type( 'BAS' );

				$line_1 = $transaction->new_line();

				$line_1->set_type( 'detail' );
				$line_1->set_id( '1' );
				$line_1->set_dimension_1( $dimension_type_pnl->new_dimension( '4008' ) );
				$line_1->set_debit_credit( 'debit' );
				$line_1->set_value( '435.55' );

				$line_2 = $transaction->new_line();

				$line_2->set_type( 'detail' );
				$line_2->set_id( '2' );
				$line_2->set_dimension_1( $dimension_type_bas->new_dimension( '1300' ) );
				$line_2->set_dimension_2( $dimension_type_crd->new_dimension( '1000' ) );
				$line_2->set_debit_credit( 'credit' );
				$line_2->set_value( '435.55' );
				$line_2->set_invoice_number( '11001770' );
				$line_2->set_description( 'Invoice paid' );

				?>
				<textarea name="pronamic_twinfield_xml" class="code-mirror-xml"><?php echo \esc_textarea( $transaction->to_xml() ); ?></textarea>
				
				<h2>Transaction Request</h2>

				<?php

				$transaction_request = new Transactions\TransactionRequest( $transaction, 'temporary' );

				?>

				<form method="post" action="">
					<div>
						<label>
							XML
							<textarea name="pronamic_twinfield_xml" class="code-mirror-xml"><?php echo \esc_textarea( $transaction_request->to_xml() ); ?></textarea>
						</label>
					</div>

					<div>
						<input type="hidden" name="pronamic_twinfield_office_code" value="<?php echo \esc_attr( $office->get_code() ); ?>" />

						<button type="submit" name="pronamic_twinfield_process_xml">Submit</button>
					</div>
				</form>

				<h2>Declarations</h2>

				<?php

				$declarations_service = $client->get_service( 'declarations' );

				$summaries = $declarations_service->get_all_summaries( $office );

				\usort(
					$summaries,
					function( $a, $b ) {
						return -\strnatcmp( $a->get_id(), $b->get_id() );
					} 
				);

				?>

				<table>
					<thead>
						<tr>
							<th scope="col" rowspan="2">ID</th>
							<th scope="col" rowspan="2">Document Code</th>
							<th scope="col" rowspan="2">Name</th>
							<th scope="col" colspan="2">Document Time Frame</th>
							<th scope="col" colspan="3">Status</th>
							<th scope="col" colspan="2">Assignee</th>
							<th scope="col" colspan="2">Company</th>
						</tr>
						<tr>
							<th scope="col">Year</th>
							<th scope="col">Period</th>

							<th scope="col">Description</th>
							<th scope="col">StepIndex</th>
							<th scope="col">Extra Information</th>

							<th scope="col">Code</th>
							<th scope="col">Name</th>

							<th scope="col">Code</th>
							<th scope="col">Name</th>
						</tr>
					</thead>

					<tbody>
						
						<?php foreach ( $summaries as $summary ) : ?>

							<tr>
								<td><code><?php echo \esc_html( $summary->get_id() ); ?></code></td>
								<td><code><?php echo \esc_html( $summary->get_document_code() ); ?></code></td>
								<td><?php echo \esc_html( $summary->get_name() ); ?></td>

								<td><?php echo \esc_html( $summary->get_document_time_frame()->get_year() ); ?></td>
								<td><?php echo \esc_html( $summary->get_document_time_frame()->get_period() ); ?></td>

								<td><?php echo \esc_html( $summary->get_status()->get_description() ); ?></td>
								<td><?php echo \esc_html( $summary->get_status()->get_step_index() ); ?></td>
								<td><?php echo \esc_html( $summary->get_status()->get_extra_information() ); ?></td>

								<td><code><?php echo \esc_html( $summary->get_assignee()->get_code() ); ?></code></td>
								<td><?php echo \esc_html( (string) $summary->get_assignee()->get_name() ); ?></td>

								<td><code><?php echo \esc_html( $summary->get_company()->get_code() ); ?></code></td>
								<td><?php echo \esc_html( (string) $summary->get_company()->get_name() ); ?></td>
							</tr>

						<?php endforeach; ?>

					</tbody>
				</table>

				<?php

				$summary = \reset( $summaries );

				$xbrl = null;

				if ( false !== $summary ) {
					$xbrl = $declarations_service->get_xbrl_by_summary( $summary );
				}

				if ( null !== $xbrl ) : 
					?>

					<h2>Declaration XBRL</h2>

					<?php var_dump( $xbrl ); ?>

				<?php endif; ?>

				<h2>Years</h2>

				<?php

				$periods_service = $client->get_service( 'periods' );

				$years = $periods_service->get_years( $office );

				$years = \array_reverse( $years );

				?>
				<ul>

					<?php foreach ( $years as $year ) : ?>

						<li>
							<?php echo \esc_html( $year ); ?>
						</li>

					<?php endforeach; ?>

				</ul>

				<?php

				$year = \date( 'Y' );

				$periods = $periods_service->get_periods( $office, $year );

				?>
				<h2><?php \printf( 'Periods Year: %s', $year ); ?></h2>

				<table>
					<thead>
						<tr>
							<th scope="col">Number</th>
							<th scope="col">Name</th>
							<th scope="col">Is Open</th>
							<th scope="col">End Date</th>
						</tr>
					</thead>

					<tbody>
						
						<?php foreach ( $periods as $period ) : ?>

							<tr>
								<td><?php echo \esc_html( (string) $period->get_number() ); ?></td>
								<td><?php echo \esc_html( $period->get_name() ); ?></td>
								<td><?php echo \esc_html( $period->is_open() ? 'Open' : 'Closed' ); ?></td>
								<td>
									<?php

									$end_date = $period->get_end_date();

									echo esc_html( null === $end_date ? '' : $end_date->format( 'd-m-Y' ) );

									?>
								</td>
							</tr>

						<?php endforeach; ?>

					</tbody>
				</table>

				<h2>Browse Data <code>010</code></h2>

				<?php

				$xml_processor = $client->get_xml_processor();

				$xml_processor->set_office( $office );

				$browser = new Browse\Browser( $xml_processor );

				/**
				 * 2. Read the browse definition.
				 * 
				 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Request/BrowseData#Read-the-browse-definition
				 */
				$browse_read_request = new Browse\BrowseReadRequest( $office->get_code(), '010' );

				$browse_read_response = $xml_processor->process_xml_string( new ProcessXmlString( $browse_read_request->to_xml() ) );

				/**
				 * 3. Fill in the selection criteria.
				 * 
				 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Request/BrowseData#Fill-in-the-selection-criteria
				 */
				$browse_definition = new Browse\BrowseDefinition( \simplexml_load_string( $browse_read_response->get_result() ) );

				$browse_definition->get_column( 'fin.trs.head.yearperiod' )->between( '198501', '202201' );

				/**
				 * Compose the browse request and send it.
				 * 
				 * @link https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Request/BrowseData#Compose-the-browse-request-and-send-it
				 */
				$xml_columns = $browse_definition->get_xml_columns();

				$xml_columns['optimize'] = 'true';

				$xml_columns_string = $xml_columns->asXML();

				$xml_columns_string = '<columns code="010" optimize="true"> <column>
    <field>fin.trs.line.matchstatus</field>
    <operator>equal</operator>
    <from>available</from>
  </column> </columns>';

				$browse_request_document = new \DOMDocument();

				$browse_request_document->preserveWhiteSpace = false;
				$browse_request_document->formatOutput       = true;

				$browse_request_document->loadXML( $xml_columns_string );

				$browse_response = $xml_processor->process_xml_string( new ProcessXmlString( $xml_columns_string ) );

				$browse_response_document = new \DOMDocument();

				$browse_response_document->preserveWhiteSpace = false;
				$browse_response_document->formatOutput       = true;

				$browse_response_document->loadXML( $browse_response->get_result() );

				?>

				<h3>2. Read the browse definition</h3>

				<p>
					<a href="https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Request/BrowseData#Read-the-browse-definition">https://accounting.twinfield.com/webservices/documentation/#/ApiReference/Request/BrowseData#Read-the-browse-definition</a>
				</p>

				<h4>Request</h4>

				<textarea class="code-mirror-xml"><?php echo \esc_textarea( $browse_read_request->to_xml() ); ?></textarea>

				<h4>Response</h4>

				<textarea class="code-mirror-xml"><?php echo \esc_textarea( $browse_read_response->get_result() ); ?></textarea>

				<h3>4. Compose and send the (browse) query to Twinfield</h3>

				<h4>Request</h4>

				<textarea class="code-mirror-xml"><?php echo \esc_textarea( $browse_request_document->saveXML() ); ?></textarea>
 
				<h4>Response</h4>

				<textarea class="code-mirror-xml"><?php echo \esc_textarea( $browse_response_document->saveXML() ); ?></textarea>

			<?php endif; ?>

		<?php endif; ?>

		<p>
			<?php

			$state = \bin2hex( \openssl_random_pseudo_bytes( 32 ) );

			$url = $openid_connect_client->get_authorize_url( $state );

			\printf(
				'<a href="%s">Connect with Twinfield</a>',
				\esc_url( $url )
			);

			?>
		</p>

		<script src="https://unpkg.com/codemirror@5.62.2/lib/codemirror.js"></script>
		<script src="https://unpkg.com/codemirror@5.62.2/mode/xml/xml.js"></script>

		<script type="text/javascript">
			var elements = document.getElementsByClassName( 'code-mirror-xml' );

			Array.from( elements ).forEach( ( element ) => {
				editor = CodeMirror.fromTextArea( element, {
					lineNumbers: true,
					mode: 'application/xml'
				} );
			} );
		</script>
	</body>
</html>
