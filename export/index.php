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

$dimension_types = [
	'BAS' => 'Balancesheet',
	'PNL' => 'Profit and Loss',
	'CRD' => 'Accounts Payable',
	'DEB' => 'Accounts Receivable',
	'KPL' => 'Cost centers',
	'AST' => 'Assets',
	'PRJ' => 'Projects',
	'ACT' => 'Activities',
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

function export_dimensions( $client, $office, $dimtype, $path, $options = [] ) {
	$finder = $client->get_finder();

	$xml_processor = $client->get_xml_processor();

	$finder->set_office( $office );

	$xml_processor->set_office( $office );

	$first_row = 1;
	$max_rows  = 100;

	do {
		$search = new Search(
			'DIM',
			'*',
			0,
			$first_row,
			$max_rows,
			[
				'dimtype' => $dimtype,
				...$options,
			] 
		);
		
		$response = $finder->search( $search );

		$data = $response->get_data();

		$items = $data->get_items();

		foreach ( $data as $item ) {
			$filename = \sprintf(
				__DIR__ . '/' . $path . '-office-%s-code-%s.xml',
				$office->get_code(),
				$item[0]
			);

			if ( file_exists( $filename ) ) {
				continue;
			}

			$xml = \sprintf(
				'<read><type>dimensions</type><office>%s</office><dimtype>%s</dimtype><code>%s</code></read>',
				$office->get_code(),
				$dimtype,
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

		$first_row += $max_rows;
	} while ( count( $items ) > 0 );
}

function export_customer_transactions( $client, $office, $code, $period, $dir ) {
	$xml_processor = $client->get_xml_processor();

	$xml_processor->set_office( $office );

	$browse_request = \sprintf(
		'
		<columns code="000">
			<column>
				<field>fin.trs.head.code</field>
				<operator>equal</operator>
				<from>%s</from>
			</column>
			<column>
				<field>fin.trs.head.yearperiod</field>
				<operator>equal</operator>
				<from>%s</from>
			</column>
		</columns>
		',
		$code,
		$period
	);

	$res = $xml_processor->process_xml_string( $browse_request );

	$simple_xml = simplexml_load_string( (string) $res );

	foreach ( $simple_xml->tr as $row ) {
		$office_code        = (string) $row->key->office;
		$journal_code       = (string) $row->key->code;
		$transaction_number = (string) $row->key->number;

		$filename = $dir . "/transaction-office-$office_code-code-$journal_code-number-$transaction_number.xml";

		echo $filename, PHP_EOL;

		if ( file_exists( $filename ) ) {
			continue;
		}

		$xml = "<read><type>transaction</type><office>$office_code</office><code>$journal_code</code><number>$transaction_number</number></read>";

		$res = $xml_processor->process_xml_string( $xml );

		$doc = new DOMDocument();

		$doc->preserveWhitespace = false;
		$doc->formatOutput       = true;

		$doc->loadXML( $res );

		$doc->save( $filename );
	}
}

$periods_service = new Periods\PeriodsService( $client );

foreach ( $offices as $office ) {
	// export_articles( $client, $office );
	// export_dimensions( $client, $office, 'ACT', 'activities/activitiy', [] );
	// export_dimensions( $client, $office, 'DEB', 'customers/customer', [ 'includehidden' => '1' ] );
	// export_dimensions( $client, $office, 'CRD', 'suppliers/supplier', [ 'includehidden' => '1' ] );
	// export_dimensions( $client, $office, 'AST', 'fixed-assets/fixed-asset' );
	// export_dimensions( $client, $office, 'KPL', 'cost-centers/cost-center', [ 'includehidden' => '1' ] );
	// export_dimensions( $client, $office, 'BAS', 'general-ledger-accounts-bas/general-ledger-account-bas', [ 'includehidden' => '1' ] );
	// export_dimensions( $client, $office, 'PNL', 'general-ledger-accounts-pnl/general-ledger-account-pnl', [ 'includehidden' => '1' ] );
	// export_dimensions( $client, $office, 'PRJ', 'projects/project', [] );

	$years = $periods_service->get_years( $office );

	$transaction_types = $client->get_transaction_types( $office );

	foreach ( $transaction_types as $transaction_type ) {
		$transaction_type_code = $transaction_type->get_code();

		foreach ( $years as $year ) {
			if ( $year < 2005 ) {
				continue;
			}

			$periods = $periods_service->get_periods( $office, $year );

			foreach ( $periods as $period ) {
				$period_string = sprintf( '%04d/%02d', $year, $period->get_number() );

				echo "Transaction type code: $transaction_type_code", PHP_EOL;
				echo "Year: $year", PHP_EOL;
				echo "Period: $period_string", PHP_EOL;

				$dir = __DIR__ . "/transactions/$transaction_type_code/$period_string";

				if ( ! is_dir( $dir ) ) {
					mkdir( $dir, 0777, true );
				}

				export_customer_transactions( $client, $office, $transaction_type_code, $period_string, $dir );
			}
		}   
	}

	// export_customer_transactions( $client, $office );
}
