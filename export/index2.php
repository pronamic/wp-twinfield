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
	function ( $client ) use ( $authentication_file ): void {
		\file_put_contents( $authentication_file, \wp_json_encode( $client->get_authentication(), \JSON_PRETTY_PRINT ) );
	} 
);

$organisation = $client->get_organisation();

$offices = $client->get_offices();

$invoice_types = [
	'FACTPRNLEO' => [
		'2011' => range( '115001', '115033' ),
		'2012' => range( '125000', '125034' ),
		'2013' => range( '135000', '135029' ),
		'2014' => range( '145001', '145013' ),
		'2015' => range( '155001', '155003' ),
	],
	'FACTUUR'    => [
		'2011' => array_merge(
			range( '7', '12' ),
			range( '110015', '110579' )
		),
		'2012' => range( '120000', '121048' ),
		'2013' => range( '130000', '131909' ),
		'2014' => range( '140000', '141995' ),
		'2015' => range( '1500000', '1501265' ),
		'2016' => range( '1600000', '1601532' ),
		'2017' => range( '1700000', '1701283' ),
		'2018' => range( '1800000', '1801265' ),
		'2019' => range( '1900000', '1901194' ),
		'2020' => range( '2000000', '2001048' ),
		'2021' => range( '2100000', '2100908' ),
		'2022' => range( '2200000', '2200886' ),
		'2023' => range( '2300000', '2300945' ),
	],
];

foreach ( $offices as $office ) {
	$xml_processor = $client->get_xml_processor();

	$xml_processor->set_office( $office );

	$office_code = $office->get_code();

	foreach ( $invoice_types as $invoice_type => $years ) {
		foreach ( $years as $year => $invoice_numbers ) {
			foreach ( $invoice_numbers as $invoice_number ) {
				echo "Office: $office_code", PHP_EOL;
				echo "Invoice type: $invoice_type", PHP_EOL;
				echo "Year: $year", PHP_EOL;
				echo "Invoice number: $invoice_number", PHP_EOL;

				$dir = __DIR__ . "/sales-invoices/$invoice_type/$year";

				if ( ! is_dir( $dir ) ) {
					mkdir( $dir, 0777, true );
				}

				$filename = $dir . "/sales-invoice-$invoice_type-$invoice_number.xml";

				echo "File: $filename", PHP_EOL;

				if ( file_exists( $filename ) ) {
					continue;
				}

				$xml = "<read><type>salesinvoice</type><office>$office_code</office><code>$invoice_type</code><invoicenumber>$invoice_number</invoicenumber></read>";

				$res = $xml_processor->process_xml_string( $xml );

				$doc = new DOMDocument();

				$doc->preserveWhitespace = false;
				$doc->formatOutput       = true;

				$doc->loadXML( $res );

				$doc->save( $filename );
			}
		}
	}
}
