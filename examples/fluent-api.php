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

		<h2>🎯 New Fluent API - Offices with includeId</h2>

		<?php

		$finder = $client->get_finder();

		// New fluent API - super easy! 🚀
		$offices = $finder->offices()
			->include_id()
			->limit( 10 )
			->get_offices();

		echo '<h3>Offices (New API)</h3>';
		echo '<pre>';
		var_dump( $offices );
		echo '</pre>';

		?>

		<h2>🎯 New Fluent API - Fixed Assets Dimensions</h2>

		<?php

		// Query fixed assets with the new fluent API
		$fixed_assets_data = $finder->dimensions()
			->fixed_assets()
			->pattern( '*' )
			->limit( 20 )
			->get();

		echo '<h3>Fixed Assets</h3>';
		echo '<pre>';
		var_dump( $fixed_assets_data );
		echo '</pre>';

		?>

		<h2>🎯 New Fluent API - Customers Modified Recently</h2>

		<?php

		// Get customers modified in the last year
		$recent_customers = $finder->dimensions()
			->customers()
			->modifiedSince( '-1 year' )
			->limit( 15 )
			->items();

		echo '<h3>Recent Customers</h3>';
		echo '<pre>';
		var_dump( $recent_customers );
		echo '</pre>';

		?>

		<h2>🎯 New Fluent API - Articles Search</h2>

		<?php

		// Search articles
		$articles = $finder->articles()
			->pattern( '*' )
			->limit( 10 )
			->items();

		echo '<h3>Articles</h3>';
		echo '<pre>';
		var_dump( $articles );
		echo '</pre>';

		?>

		<h2>📚 Old API (Still Works) - Offices</h2>

		<?php

		// Old API still works for backward compatibility
		$first_row = 1;
		$max_rows  = 5;

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

		echo '<h3>Old API Response</h3>';
		echo '<pre>';
		var_dump( $response );
		echo '</pre>';

		?>
	</body>
</html>

