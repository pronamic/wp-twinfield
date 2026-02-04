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

		<?php

		$finder = $client->get_finder();

		$first_row = 1;
		$max_rows  = 100;

		$timezone = new \DateTimeZone( 'UTC' );

		$modified_since_datetime = new \DateTimeImmutable( '-1 year', $timezone );

		$options = [
			'includeid' => '1',
		];

		$search = new \Pronamic\WordPress\Twinfield\Finder\Search(
			\Pronamic\WordPress\Twinfield\Finder\FinderTypes::OFF,
			'*',
			0,
			$first_row,
			$max_rows,
			$options
		);

		$response = $finder->search( $search );

		echo '<pre>';
		var_dump( $response );
		echo '</pre>';

		$data = $response->get_data();

		$items = $data->get_items();


		?>
	</body>
</html>
