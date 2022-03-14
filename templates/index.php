<?php
/**
 * Twinfield index template.
 *
 * @link https://github.com/wp-twinfield/wp-twinfield/blob/develop/templates/sales-invoice.php
 * @package Pronamic/WordPress/Twinfield
 */

get_header();

// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
var_dump( \get_query_var( 'pronamic_twinfield_route', null ) );

// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
var_dump( $response->get_data() );

// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
var_dump( $response );

get_footer();
