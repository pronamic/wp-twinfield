<?php
/**
 * Twinfield Fixes Assets API example.
 *
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

if ( isset( $authentication ) ) {
	$client = new Client( $openid_connect_client, $authentication );

	$client->set_authentication_refresh_handler(
		function ( $client ) use ( $authentication_file ): void {
            // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents
			\file_put_contents( $authentication_file, \wp_json_encode( $client->get_authentication(), \JSON_PRETTY_PRINT ) );
		}
	);
}

$fixed_assets_service = new FixedAssets\FixedAssetsService( $client );

$organisation = $client->get_organisation();

?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<title>Twinfield Finder API Examples</title>

		<link rel="stylesheet" type="text/css" href="https://unpkg.com/codemirror@5.62.2/lib/codemirror.css" />
	</head>

	<body>
		<h1>Twinfield Finder API Examples</h1>

		<p>
			<a href="https://developers.twinfield.com/">https://developers.twinfield.com/</a>
		</p>

		<h2>🎯 New Fluent API - Offices with includeId</h2>

		<?php

		$finder = $client->get_finder();

		$offices = $finder->offices()
			->include_id()
			->limit( 10 )
			->get_offices();

		?>
		<h3>Offices</h3>

		<table>
			<thead>
				<tr>
					<th>Code</th>
					<th>Name</th>
					<th>ID</th>
				</tr>
			</thead>

			<tbody>

				<?php foreach ( $offices as $office ) : ?>

					<tr>
						<td><?php echo \htmlspecialchars( $office->get_code() ); ?></td>
						<td><?php echo \htmlspecialchars( $office->get_name() ); ?></td>
						<td><?php echo \htmlspecialchars( $office->id ); ?></td>
					</tr>

				<?php endforeach; ?>

			</tbody>
		</table>

		<?php foreach ( $offices as $office ) : ?>

			<h3>Fixed Assets for Office: <?php echo \htmlspecialchars( $office->get_name() ); ?></h3>

			<?php

			$assets = $fixed_assets_service->assets( $organisation->get_uuid(), $office->id )
				->limit( 100 )
				->fields( '*' )
				->get();

			?>

			<table>
				<thead>
					<tr>
						<th scope="col" rowspan="2">Code</th>
						<th scope="col" rowspan="2">Description</th>
						<th scope="col" rowspan="2">Version</th>
						<th scope="col" rowspan="2">Status</th>
						<th scope="col" rowspan="2">ID</th>
						<th scope="col">Youngest Balances</th>
					</tr>
					<tr>
						<th scope="col">Net Book Value</th>
						<th scope="col">Purchase Value</th>
					</tr>
				</thead>

				<tbody>

					<?php foreach ( $assets as $asset ) : ?>

						<tr>
							<td><?php echo \htmlspecialchars( $asset->code ); ?></td>
							<td><?php echo \htmlspecialchars( $asset->description ); ?></td>
							<td><?php echo \htmlspecialchars( $asset->version ); ?></td>
							<td><?php echo \htmlspecialchars( $asset->status ); ?></td>
							<td><?php echo \htmlspecialchars( $asset->id ); ?></td>
							<td><?php echo \htmlspecialchars( $asset->youngest_balances?->net_book_value ); ?></td>
							<td><?php echo \htmlspecialchars( $asset->youngest_balances?->purchase_value ); ?></td>
						</tr>

					<?php endforeach; ?>

				</tbody>
			</table>

		<?php endforeach; ?>

	</body>
</html>

