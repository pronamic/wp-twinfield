<?php

namespace Pronamic\WordPress\Twinfield;

use DOMDocument;
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
	'ART' => [],
];


function export_articles( $client, $office ) {
	$finder = $client->get_finder();

	$xml_processor = $client->get_xml_processor();

	$finder->set_office( $office );

	$xml_processor->set_office( $office );

	$search = new Search( 'ART', '*', 0, 1, 100, [] );

	$response = $finder->search( $search );

	foreach ( $response->get_data() as $item ) {
		$filename = \sprintf(
			__DIR__ . '/articles/article-office-%s-code-%s.xml',
			$office->get_code(),
			$item[0]
		);

		if ( file_exists( $filename ) ) {
			continue;
		}

		$xml = \sprintf(
			'<read><type>article</type><office>%s</office><code>%s</code></read>',
			$office->get_code(),
			$item[0]
		);

		$res = $xml_processor->process_xml_string( $xml );

		$doc = new DOMDocument();

		$doc->preserveWhitespace = false;
		$doc->formatOutput       = true;

		$doc->loadXML( $res );

		$doc->save( $filename );

		echo $filename, PHP_EOL;
	}
}

foreach ( $offices as $office ) {
	export_articles( $client, $office );
}
