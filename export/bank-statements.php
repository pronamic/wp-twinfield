<?php

namespace Pronamic\WordPress\Twinfield;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
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

$offices = $client->get_offices();

$start_date = new DateTimeImmutable( '2011-01-01' );
$end_date   = new DateTimeImmutable();

$interval = new DateInterval( 'P1D' );
$period   = new DatePeriod( $start_date, $interval, $end_date );

$bank_statements_service = new BankStatements\BankStatementsService( $client );

foreach ( $offices as $office ) {
	foreach ( $period as $date ) {
		$start_date = $date->setTime( 0, 0, 0 );
		$end_date   = $start_date->modify( '+1 day' );

		$dir = __DIR__ . '/bank-statements/' . $date->format( 'Y' );

		if ( ! is_dir( $dir ) ) {
			mkdir( $dir, 0777, true );
		}

		$filename = $dir . '/bank-statements-' . $date->format( 'Y-m-d' ) . '.json';

		echo 'Office: ', $office->get_code(), PHP_EOL;
		echo 'Date: ', $date->format( 'Y-m-d' ), PHP_EOL;
		echo 'File: ', $filename, PHP_EOL;

		if ( file_exists( $filename ) ) {
			continue;
		}

		$query = new BankStatements\BankStatementsQuery( $start_date, $end_date, true );

		$result = $bank_statements_service->get_bank_statements( $office, $query );

		file_put_contents( $filename, json_encode( $result, JSON_PRETTY_PRINT ) );
	}
}
