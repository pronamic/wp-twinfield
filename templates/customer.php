<?php
/**
 * Twinfield sales invoice template.
 *
 * @link https://github.com/wp-twinfield/wp-twinfield/blob/develop/templates/customer.php
 * @package Pronamic/WordPress/Twinfield
 */

get_header();

$customer = $dimension;

$office = $customer->get_office();

$organisation = $office->get_organisation();

$twinfield = $organisation->get_twinfield();

?>

<div id="container">
	<div id="content" role="main">

		<div class="page-header">
			<h1><?php echo esc_html( $customer->get_name() ); ?></h1>
		</div>

		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Contact', 'pronamic-twinfield' ); ?></h3>
			</header>

			<div class="content">
				<dl class="row">
					<dt class="col-sm-2"><?php esc_html_e( 'Name', 'pronamic-twinfield' ); ?></dt>
					<dd class="col-sm-10"><?php echo esc_html( $customer->get_name() ); ?></dd>

					<dt class="col-sm-2"><?php esc_html_e( 'Office', 'pronamic-twinfield' ); ?></dt>
					<dd class="col-sm-10"><?php echo esc_html( $customer->get_office() ); ?></dd>
				</dl>
			</div>
		</div>

		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Financials', 'pronamic-twinfield' ); ?></h3>
			</header>

			<div class="content">
				<dl class="row">
					<dt class="col-sm-2"><?php esc_html_e( 'Due Days', 'pronamic-twinfield' ); ?></dt>
					<dd class="col-sm-10"><?php echo esc_html( $customer->get_financials()->get_due_days() ); ?></dd>

					<dt class="col-sm-2"><?php esc_html_e( 'Electronic Billing', 'pronamic-twinfield' ); ?></dt>
					<dd class="col-sm-10"><?php echo esc_html( $customer->get_financials()->get_ebilling() ? __( 'Yes', 'pronamic-twinfield' ) : __( 'No', 'pronamic-twinfield' ) ); ?></dd>

					<dt class="col-sm-2"><?php esc_html_e( 'Email', 'pronamic-twinfield' ); ?></dt>
					<dd class="col-sm-10"><?php echo esc_html( $customer->get_financials()->get_ebillmail() ); ?></dd>
				</dl>
			</div>
		</div>

		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Credit Management', 'pronamic-twinfield' ); ?></h3>
			</header>

			<div class="content">
				<dl class="row">
					<dt class="col-sm-2"><?php esc_html_e( 'Send Reminder', 'pronamic-twinfield' ); ?></dt>
					<dd class="col-sm-10">
					<?php

					$send_reminder = $customer->get_credit_management()->get_send_reminder();

					switch ( $send_reminder ) {
						case 'true':
							esc_html_e( 'Yes', 'pronamic-twinfield' );
							break;
						case 'email':
							esc_html_e( 'Yes, by e-mail', 'pronamic-twinfield' );
							break;
						case 'false':
							esc_html_e( 'No', 'pronamic-twinfield' );
							break;
						default:
							echo esc_html( $send_reminder );
							break;
					}

					?>
					</dd>

					<dt class="col-sm-2"><?php esc_html_e( 'Reminder Email', 'pronamic-twinfield' ); ?></dt>
					<dd class="col-sm-10"><?php echo esc_html( $customer->get_credit_management()->get_reminder_email() ); ?></dd>
				</dl>
			</div>
		</div>

		<div class="panel">
			<header>
				<h3><?php esc_html_e( 'Addresses', 'pronamic-twinfield' ); ?></h3>
			</header>

			<div class="content">

				<?php foreach ( $customer->get_addresses() as $address ) : ?>

					<table class="table table-striped">
						<col width="150" />

						<tr>
							<th scope="row"><?php esc_html_e( 'Default', 'pronamic-twinfield' ); ?></th>
							<td><?php $address->is_default() ? esc_html_e( 'Yes', 'pronamic-twinfield' ) : esc_html_e( 'No', 'pronamic-twinfield' ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Type', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_type() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Name', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_name() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Contact', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_field_1() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Address', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_field_2() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Postal Code', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_postcode() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'City', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_city() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Country', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_country() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Email', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_email() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Phone Number', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_telephone() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Fax Number', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_telefax() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'VAT Number', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_field_4() ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'COC Number', 'pronamic-twinfield' ); ?></th>
							<td><?php echo esc_html( $address->get_field_5() ); ?></td>
						</tr>
					</table>

					<hr />

				<?php endforeach; ?>

			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
