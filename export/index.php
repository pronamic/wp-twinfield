<?php

namespace Pronamic\WordPress\Twinfield;

use Pronamic\WordPress\Twinfield\Finder\Search;

require __DIR__ . '/../vendor/autoload.php';

// WorDBless.
\WorDBless\Load::load();

// Load.
$file = __DIR__ . '/client-secret.json';

$openid_connect_client = Authentication\OpenIdConnectClient::from_json_file( $file );

// Authentication.
$authentication_file = __DIR__ . '/authentication-secret.json';

$authentication = Authentication\AuthenticationInfo::from_object( \json_decode( \file_get_contents( $authentication_file, true ) ) );

// Client.
$client = new Client( $openid_connect_client, $authentication );

$client->set_authentication_refresh_handler(
	function ( $client ) use ( $authentication_file ) {
		\file_put_contents( $authentication_file, \wp_json_encode( $client->get_authentication(), \JSON_PRETTY_PRINT ) );
	} 
);

$organisation = $client->get_organisation();

$dimension_types = [
	'BAS'=> 'Balancesheet',
	'PNL'=> 'Profit and Loss',
	'CRD'=> 'Accounts Payable',
	'DEB'=> 'Accounts Receivable',
	'KPL'=> 'Cost centers',
	'AST'=> 'Assets',
	'PRJ'=> 'Projects',
	'ACT'=> 'Activities',
];

$offices = $client->get_offices();

$finder_types = [
	'ART',
];

$finder = $client->get_finder();

foreach ( $offices as $office ) {
	$finder->set_office( $office );

	foreach ( $finder_types as $type ) {
		$search = new Search(
			$type,
			'*',
			0,
			1,
			100,
			[
				'hidden' => '1',
			]
		);

		$response = $finder->search( $search );

		var_dump( $response );
	}
}
