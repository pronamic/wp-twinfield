<?php
/**
 * Save bank statement controller
 *
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

use DateTimeImmutable;
use DateTimeZone;
use Pronamic\WordPress\Twinfield\BankStatements\BankStatementsService;
use Pronamic\WordPress\Twinfield\BankStatements\BankStatementsByCreationDateQuery;
use Pronamic\WordPress\Twinfield\BankStatements\BankStatementsQuery;
use WP_CLI;
use WP_REST_Request;

/**
 * Save bank statement controller class
 */
class SaveBankStatementController {
	/**
	 * Plugin.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Construct REST controller.
	 *
	 * @param Plugin $plugin Plugin.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'cli_init', $this->cli_init( ... ) );

		\add_action( 'pronamic_twinfield_save_bank_statements', $this->save_bank_statements( ... ) );
		\add_action( 'pronamic_twinfield_save_office_bank_statements', $this->save_office_bank_statements( ... ), 10, 4 );

		\add_action( 'pronamic_twinfield_save_unposted_bank_statements', $this->save_unposted_bank_statements( ... ) );
	}

	/**
	 * CLI intialize.
	 *
	 * @return void
	 */
	private function cli_init() {
		/**
		 * Save bank statements.
		 *
		 * Example:
		 * wp pronamic-twinfield save-bank-statements --authorization=5337 --date_from=2025-04-01 --date_to=2025-04-17
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-bank-statements',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				$assoc_args['date_from'] ??= '';
				$assoc_args['date_to']   ??= '';

				$this->save_bank_statements( $assoc_args['authorization'], $assoc_args['date_from'], $assoc_args['date_to'] );
			}
		);

		/**
		 * Save office bank statements.
		 *
		 * Example:
		 * wp pronamic-twinfield save-office-bank-statements --authorization=5337 --office_code=1368 --date_from=2025-03-01 --date_to=2025-04-01
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-office-bank-statements',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				if ( ! \array_key_exists( 'office_code', $assoc_args ) ) {
					WP_CLI::error( 'Office code argument missing.' );
				}

				$assoc_args['date_from'] ??= 'midnight -2 days';
				$assoc_args['date_to']   ??= 'now';

				$this->save_office_bank_statements( $assoc_args['authorization'], $assoc_args['office_code'], $assoc_args['date_from'], $assoc_args['date_to'] );
			}
		);

		/**
		 * Save office bank statements by statement date.
		 *
		 * Example:
		 * wp pronamic-twinfield save-office-bank-statements-by-statement-date --authorization=5337 --office_code=1368 --date_from=2025-03-01 --date_to=2025-04-01
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-office-bank-statements-by-statement-date',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				if ( ! \array_key_exists( 'office_code', $assoc_args ) ) {
					WP_CLI::error( 'Office code argument missing.' );
				}

				$assoc_args['date_from'] ??= 'midnight -2 days';
				$assoc_args['date_to']   ??= 'now';

				$this->save_office_bank_statements_by_statement_date( $assoc_args['authorization'], $assoc_args['office_code'], $assoc_args['date_from'], $assoc_args['date_to'] );
			}
		);

		/**
		 * Save unposted bank statements.
		 *
		 * Example:
		 * wp pronamic-twinfield save-unposted-bank-statements --authorization=5337
		 */
		WP_CLI::add_command(
			'pronamic-twinfield save-unposted-bank-statements',
			function ( $args, $assoc_args ): void {
				if ( ! \array_key_exists( 'authorization', $assoc_args ) ) {
					WP_CLI::error( 'Authorization argument missing.' );
				}

				$this->save_unposted_bank_statements( $assoc_args['authorization'] );
			}
		);
	}

	/**
	 * Save unposted bank statements.
	 *
	 * @param string|int $authorization Authorization.
	 * @return void
	 */
	private function save_unposted_bank_statements( $authorization ) {
		global $wpdb;

		$timezone = new DateTimeZone( 'UTC' );

		$query = "
			SELECT
				office.code AS office_code,
				MIN( bank_statement.date ) AS start_date,
				LEAST( DATE_ADD( MIN( bank_statement.date ), INTERVAL 3 MONTH ), MAX( bank_statement.date ) ) AS end_date
			FROM
				wp_twinfield_bank_statements AS bank_statement
					INNER JOIN
				wp_twinfield_offices AS office
						ON bank_statement.office_id = office.id
			WHERE
				bank_statement.local_status NOT IN ( 'disappeared' )
					AND
				office.local_status NOT IN ( 'disappeared' )
					AND
				transaction_number IS NULL
			GROUP BY
				bank_statement.office_id
			;
		";

		$results = $wpdb->get_results( $query );

		foreach ( $results as $item ) {
			$date_from = new DateTimeImmutable( $item->start_date, $timezone );
			$date_to   = new DateTimeImmutable( $item->end_date, $timezone );

			$action_id = \as_enqueue_async_action(
				'pronamic_twinfield_save_office_bank_statements',
				[
					'authorization' => $authorization,
					'office_code'   => $item->office_code,
					'date_from'     => $date_from->format( 'Y-m-d H:i:s' ),
					'date_to'       => $date_to->format( 'Y-m-d H:i:s' ),
				],
				'pronamic-twinfield'
			);

			$this->log(
				\sprintf(
					'Saving administration unposted bank statements is scheduled, authorization post ID: %s, office code: %s, action ID: %s.',
					$authorization,
					$item->office_code,
					$action_id
				)
			);
		}
	}

	/**
	 * Save bank statements.
	 *
	 * @param string|int $authorization    Authorization.
	 * @param string     $date_from_string Date from string.
	 * @param string     $date_to_string   Date to string.
	 * @return void
	 */
	private function save_bank_statements( $authorization, $date_from_string = '', $date_to_string = '' ) {
		global $wpdb;

		$client = $this->plugin->get_client( \get_post( $authorization ) );

		$organisation = $client->get_organisation();

		$request = new WP_REST_Request( 'GET', '/pronamic-twinfield/v1/authorizations/' . $authorization . '/offices' );

		$request->set_param( 'authorization', $authorization );

		$response = \rest_do_request( $request );

		$data = (object) $response->get_data();

		/**
		 * Template offices.
		 *
		 * Bank statements cannot be requested from template administrations.
		 */
		$where = $wpdb->prepare( "organisation.code = %s", $organisation->get_code() );

		$offices_codes = \array_map(
			fn( $office ) => $office->get_code(),
			$data->data
		);

		$where .= $wpdb->prepare( " AND office.is_template = %d", false );

		$where .= $wpdb->prepare(
			\sprintf(
				' AND office.code IN ( %s )',
				\implode( ', ', \array_fill( 0, \count( $offices_codes ), '%s' ) )
			),
			$offices_codes
		);

		$query = "
			SELECT
				office.code,
				IFNULL( save_state.saved_at, NOW() - INTERVAL 1 WEEK ) AS saved_at
			FROM
				{$wpdb->prefix}twinfield_offices AS office
					INNER JOIN
				{$wpdb->prefix}twinfield_organisations AS organisation
						ON office.organisation_id = organisation.id
					LEFT JOIN
				{$wpdb->prefix}twinfield_save_state AS save_state
						ON (
							save_state.office_id = office.id
								AND
							save_state.entity = 'bank-statements'
						)
			WHERE
				$where
			;
		";

		$results = $wpdb->get_results( $query );

		$timezone = new DateTimeZone( 'UTC' );

		$now = new DateTimeImmutable( 'now', $timezone );

		$input_date_from = ( '' === $date_from_string ) ? null : new DateTimeImmutable( $date_from_string, $timezone );
		$input_date_to   = ( '' === $date_to_string ) ? null : new DateTimeImmutable( $date_to_string, $timezone );

		foreach ( $results as $item ) {
			$date_from = $input_date_from ?? new DateTimeImmutable( $item->saved_at, $timezone );
			$date_to   = $input_date_to ?? \min( $now, $date_from->modify( '+1 week' ) );

			$action_id = \as_enqueue_async_action(
				'pronamic_twinfield_save_office_bank_statements',
				[
					'authorization' => $authorization,
					'office_code'   => $item->code,
					'date_from'     => $date_from->format( 'Y-m-d H:i:s' ),
					'date_to'       => $date_to->format( 'Y-m-d H:i:s' ),
				],
				'pronamic-twinfield'
			);

			$this->log(
				\sprintf(
					'Saving administration bank statements is scheduled, authorization post ID: %s, office code: %s, action ID: %s.',
					$authorization,
					$item->code,
					$action_id
				)
			);
		}
	}

	/**
	 * Save office bank statements.
	 *
	 * @param string|int $authorization    Authorization.
	 * @param string     $office_code      Office code.
	 * @param string     $date_from_string Date from.
	 * @param string     $date_to_string   Date to.
	 * @return void
	 */
	private function save_office_bank_statements( $authorization, $office_code, $date_from_string, $date_to_string ) {
		global $wpdb;

		$client = $this->plugin->get_client( \get_post( $authorization ) );

		$organisation = $client->get_organisation();

		$office = $organisation->office( $office_code );

		$bank_statements_service = new BankStatementsService( $client );

		$timezone = new DateTimeZone( 'UTC' );

		$now = new DateTimeImmutable( 'now', $timezone );

		$date_from = new DateTimeImmutable( $date_from_string, $timezone );
		$date_to   = new DateTimeImmutable( $date_to_string, $timezone );

		$date_to = \min( $now, $date_to );

		$query = new BankStatementsByCreationDateQuery( $date_from, $date_to, true );

		$bank_statements = $bank_statements_service->get_bank_statements_by_creation_date( $office, $query );

		$this->bank_statements_update_or_create( $bank_statements );

		$query = $wpdb->prepare(
			"
			INSERT INTO {$wpdb->prefix}twinfield_save_state ( created_at, updated_at, office_id, entity, saved_at )
			(
				SELECT
					NOW() AS created_at,
					NOW() AS updated_at,
					office.id AS office_id,
					'bank-statements' AS entity,
					%s AS saved_at
				FROM
					{$wpdb->prefix}twinfield_offices AS office
						INNER JOIN
					{$wpdb->prefix}twinfield_organisations AS organisation
							ON office.organisation_id = organisation.id
				WHERE
					organisation.code = %s
						AND
					office.code = %s
			) ON DUPLICATE KEY UPDATE
					updated_at = VALUES( updated_at ),
					saved_at = GREATEST( saved_at, VALUES( saved_at ) )
			;
			",
			$date_to->format( 'Y-m-d H:i:s' ),
			$organisation->get_code(),
			$office_code
		);

		$result = $wpdb->query( $query );

		if ( false === $result ) {
			throw new \Exception(
				\sprintf(
					'Error query %s: %s.',
					$query,
					$wpdb->last_error
				)
			);
		}
	}

	/**
	 * Save office bank statements by statement date.
	 *
	 * @param string|int $authorization    Authorization.
	 * @param string     $office_code      Office code.
	 * @param string     $date_from_string Date from.
	 * @param string     $date_to_string   Date to.
	 * @return void
	 */
	private function save_office_bank_statements_by_statement_date( $authorization, $office_code, $date_from_string, $date_to_string ) {
		$client = $this->plugin->get_client( \get_post( $authorization ) );

		$organisation = $client->get_organisation();

		$office = $organisation->office( $office_code );

		$bank_statements_service = new BankStatementsService( $client );

		$timezone = new DateTimeZone( 'UTC' );

		$date_from = new DateTimeImmutable( $date_from_string, $timezone );
		$date_to   = new DateTimeImmutable( $date_to_string, $timezone );

		$query = new BankStatementsQuery( $date_from, $date_to, true );

		$bank_statements = $bank_statements_service->get_bank_statements( $office, $query );

		$this->bank_statements_update_or_create( $bank_statements );
	}

	/**
	 * Upsert.
	 *
	 * @link https://atymic.dev/tips/laravel-8-upserts/
	 * @link https://laravel.com/docs/9.x/eloquent#upserts
	 * @link https://stackoverflow.com/questions/2634152/getting-mysql-insert-id-while-using-on-duplicate-key-update-with-php
	 * @param BankStatements $bank_statements Bank statements.
	 */
	private function bank_statements_update_or_create( $bank_statements ) {
		$orm = $this->plugin->get_orm();

		$office = $bank_statements->get_office();

		$organisation = $office->get_organisation();

		$organisation_id = $orm->first_or_create(
			$organisation,
			[
				'code' => $organisation->get_code(),
			],
			[],
		);

		$office_id = $orm->first_or_create(
			$office,
			[
				'organisation_id' => $organisation_id,
				'code'            => $office->get_code(),
			],
			[]
		);

		foreach ( $bank_statements as $bank_statement ) {
			$this->log(
				\sprintf(
					'Saving administration bank statement, office code: %s, date: %s, transaction number: %s.',
					$office->get_code(),
					$bank_statement->get_date()->format( 'Y-m-d' ),
					$bank_statement->transaction_number
				)
			);

			$data = $bank_statement->jsonSerialize();

			$bank_statement_id = $orm->update_or_create(
				$bank_statement,
				[
					'office_id' => $office_id,
					'code'      => $data->code,
					'number'    => $data->number,
					'sub_id'    => $data->sub_id,
				],
				[
					'account_number'     => $data->account_number,
					'iban'               => $data->iban,
					'date'               => $bank_statement->get_date()->format( 'Y-m-d' ),
					'currency'           => $data->currency,
					'opening_balance'    => $data->opening_balance,
					'closing_balance'    => $data->closing_balance,
					'transaction_number' => $data->transaction_number,
				]
			);

			foreach ( $bank_statement->get_lines() as $line ) {
				$this->log(
					\sprintf(
						'- Saving administration bank statement line, ID: %s.',
						$line->get_id()
					)
				);

				$data = $line->jsonSerialize();

				$bank_statement_line_id = $orm->update_or_create(
					$line,
					[
						'bank_statement_id' => $bank_statement_id,
						'line_id'           => $line->get_id(),
					],
					[
						'contra_account_number' => $data->contra_account_number,
						'contra_iban'           => $data->contra_iban,
						'contra_account_name'   => $data->contra_account_name,
						'payment_reference'     => $data->payment_reference,
						'amount'                => $data->amount,
						'base_amount'           => $data->base_amount,
						'description'           => $data->description,
						'transaction_type_id'   => $data->transaction_type_id,
						'reference'             => $data->reference,
						'end_to_end_id'         => $data->end_to_end_id,
						'return_reason'         => $data->return_reason,
					]
				);
			}
		}
	}

	/**
	 * Log.
	 *
	 * @param string $message Message.
	 * @return void
	 */
	private function log( $message ) {
		if ( \method_exists( WP_CLI::class, 'log' ) ) {
			WP_CLI::log( $message );
		}
	}
}
