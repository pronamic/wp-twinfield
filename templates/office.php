<?php
/**
 * Twinfield sales invoice template.
 *
 * @link https://github.com/wp-twinfield/wp-twinfield/blob/develop/templates/sales-invoice.php
 */

get_header();

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
					'Office %s - %s',
					$organisation->get_code(),
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

				<dt><?php esc_html_e( 'Code', 'pronamic-twinfield' ); ?></dt>
				<dd><code><?php echo esc_html( $office->get_code() ); ?></code></dd>

				<dt><?php esc_html_e( 'Name', 'pronamic-twinfield' ); ?></dt>
				<dd><?php echo esc_html( $office->get_name() ); ?></dd>

				<dt><?php esc_html_e( 'Shortname', 'pronamic-twinfield' ); ?></dt>
				<dd><?php echo esc_html( $office->get_shortname() ); ?></dd>

				<dt><?php esc_html_e( 'Status', 'pronamic-twinfield' ); ?></dt>
				<dd>
					<?php

					$status = $office->get_status();

					switch ( $status ) {
						case 'active':
							esc_html_e( 'Active', 'pronamic-twinfield' );

							break;
						default:
							echo esc_html( $status );

							break;
					}

					?>
				</dd>

				<dt><?php esc_html_e( 'Created At', 'pronamic-twinfield' ); ?></dt>
				<dd><?php echo esc_html( $office->get_created_at()->format( 'd-m-Y H:i:s' ) ); ?></dd>

				<dt><?php esc_html_e( 'Modified At', 'pronamic-twinfield' ); ?></dt>
				<dd><?php echo esc_html( $office->get_modified_at()->format( 'd-m-Y H:i:s' ) ); ?></dd>

				<dt><?php esc_html_e( 'Touched', 'pronamic-twinfield' ); ?></dt>
				<dd><?php echo esc_html( $office->get_touched() ); ?></dd>

				<dt><?php esc_html_e( 'User', 'pronamic-twinfield' ); ?></dt>
				<dd>
					<?php

					$user = $office->get_user();

					if ( null === $user ) {
						esc_html_e( '—', 'pronamic-twinfield' );
					}

					if ( null !== $user ) {
						$url = home_url( 
							strtr(
								'pronamic-twinfield/authorizations/:auth_post_id/users/:user_code',
								[
									':auth_post_id' => $request->get_param( 'post_id' ),
									':user_code'    => $user->get_code(),
								]
							)
						);

						printf(
							'<a href="%s"><code>%s</code></a>',
							esc_url( $url ),
							esc_html( $user->get_code() )
						);
					}

					?>
				</dd>
			</dl>

			<h2><?php esc_html_e( 'Menu', 'pronamic-twinfield' ); ?></h2>

			<?php

			$items = [
				[
					'label' => \__( 'Suppliers', 'pronamic-twinfield' ),
					'path'  => 'suppliers',
				],
				[
					'label' => \__( 'Customers', 'pronamic-twinfield' ),
					'path'  => 'customers',
				],
				[
					'label' => \__( 'Cost Centers', 'pronamic-twinfield' ),
					'path'  => 'cost-centers',
				],
				[
					'label' => \__( 'Fixed Assets', 'pronamic-twinfield' ),
					'path'  => 'fixed-assets',
				],
				[
					'label' => \__( 'Projects', 'pronamic-twinfield' ),
					'path'  => 'projects',
				],
				[
					'label' => \__( 'Activities', 'pronamic-twinfield' ),
					'path'  => 'activities',
				],
				[
					'label' => \__( 'Dimension Groups', 'pronamic-twinfield' ),
					'path'  => 'dimension-groups',
				],
				[
					'label' => \__( 'Dimension Types', 'pronamic-twinfield' ),
					'path'  => 'dimension-types',
				],
				[
					'label' => \__( 'Asset Methods', 'pronamic-twinfield' ),
					'path'  => 'asset-methods',
				],
				[
					'label' => \__( 'Offices', 'pronamic-twinfield' ),
					'path'  => 'offices',
				],
				[
					'label' => \__( 'Users', 'pronamic-twinfield' ),
					'path'  => 'users',
				],
				[
					'label' => \__( 'Articles', 'pronamic-twinfield' ),
					'path'  => 'articles',
				],
				[
					'label' => \__( 'Currencies', 'pronamic-twinfield' ),
					'path'  => 'currencies',
				],
				[
					'label' => \__( 'Rates', 'pronamic-twinfield' ),
					'path'  => 'rates',
				],
				[
					'label' => \__( 'VAT', 'pronamic-twinfield' ),
					'path'  => 'vat',
				],
				[
					'label' => \__( 'Declarations', 'pronamic-twinfield' ),
					'path'  => 'declarations',
				],
				[
					'label' => \__( 'Finder Types', 'pronamic-twinfield' ),
					'path'  => 'finder-types',
				],
				[
					'label' => \__( 'Hierarchies', 'pronamic-twinfield' ),
					'path'  => 'hierarchies',
				],
			];

			?>

			<table>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Label', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Page URL', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'REST URL', 'pronamic-twinfield' ); ?></th>
					</tr>
				</thead>

				<tbody>
					
					<?php foreach ( $items as $item ) : ?>

						<tr>
							<td>
								<?php echo esc_html( $item['label'] ); ?>
							</td>
							<td>
								<?php

								$url = home_url( 
									strtr(
										'pronamic-twinfield/authorizations/:auth_post_id/offices/:office_code/' . $item['path'],
										[
											':auth_post_id' => $request->get_param( 'post_id' ),
											':office_code' => $office->get_code(),
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
										'pronamic-twinfield/v1/authorizations/:auth_post_id/offices/:office_code/' . $item['path'],
										[
											':auth_post_id' => $request->get_param( 'post_id' ),
											':office_code' => $office->get_code(),
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

			<h2><?php esc_html_e( 'Finder', 'pronamic-twinfield' ); ?></h2>

			<table>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Type', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Label', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'REST URL', 'pronamic-twinfield' ); ?></th>
					</tr>
				</thead>

				<tbody>
					
					<?php foreach ( $twinfield->get_finder_types() as $type => $label ) : ?>

						<tr>
							<td>
								<code><?php echo esc_html( $type ); ?></code>
							</td>
							<td>
								<?php echo esc_html( $label ); ?>
							</td>
							<td>
								<?php

								$url = rest_url( 
									strtr(
										'pronamic-twinfield/v1/authorizations/:auth_post_id/offices/:office_code/finder/:finder_type',
										[
											':auth_post_id' => $request->get_param( 'post_id' ),
											':office_code'  => $office->get_code(),
											':finder_type'  => $type,
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

			<h2><?php esc_html_e( 'Browser', 'pronamic-twinfield' ); ?></h2>

			<table>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Type', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Label', 'pronamic-twinfield' ); ?></th>
						<th scope="col"><?php esc_html_e( 'REST URL', 'pronamic-twinfield' ); ?></th>
					</tr>
				</thead>

				<tbody>
					
					<?php foreach ( $twinfield->get_browse_codes() as $code => $label ) : ?>

						<tr>
							<td>
								<code><?php echo esc_html( $code ); ?></code>
							</td>
							<td>
								<?php echo esc_html( $label ); ?>
							</td>
							<td>
								<?php

								$url = rest_url( 
									strtr(
										'pronamic-twinfield/v1/authorizations/:auth_post_id/offices/:office_code/browse/:browse_code',
										[
											':auth_post_id' => $request->get_param( 'post_id' ),
											':office_code'  => $office->get_code(),
											':browse_code'  => $code,
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
