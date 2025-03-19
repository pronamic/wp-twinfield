<?php

namespace Pronamic\WordPress\Twinfield;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
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
	function ( $client ) use ( $authentication_file ): void {
		\file_put_contents( $authentication_file, \wp_json_encode( $client->get_authentication(), \JSON_PRETTY_PRINT ) );
	} 
);

$organisation = $client->get_organisation();

$offices = $client->get_offices();

$declarations_service = new Declarations\DeclarationsService( $client );

foreach ( $offices as $office ) {
	$declarations_service->set_office( $office );

	$summaries = $declarations_service->get_all_summaries( $office );

	$filename = __DIR__ . '/declarations/all-summaries.json';

	file_put_contents( $filename, json_encode( $summaries, JSON_PRETTY_PRINT ) );

	foreach ( $summaries->vatReturn->DeclarationSummary as $summary ) {
		$response = $declarations_service->get_xml( $summary->Id, $summary->DocumentCode );

		$filename = __DIR__ . '/declarations/declaration-' . $summary->DocumentTimeFrame->Year . '-' . $summary->DocumentCode . '-' . $summary->Id . '-xml.json';

		if ( ! file_exists( $filename ) ) {
			file_put_contents( $filename, json_encode( $response, JSON_PRETTY_PRINT ) );
		}

		$response = $declarations_service->get_xbrl( $summary->Id, $summary->DocumentCode );

		$filename = __DIR__ . '/declarations/declaration-' . $summary->DocumentTimeFrame->Year . '-' . $summary->DocumentCode . '-' . $summary->Id . '-xbrl.xml';

		if ( ! file_exists( $filename ) ) {
			$doc = new DOMDocument();

			$doc->preserveWhitespace = false;
			$doc->formatOutput       = true;

			$doc->loadXML( $response );

			$doc->save( $filename );
		}
	}
}
