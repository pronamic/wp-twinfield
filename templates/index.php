<?php

get_header();

var_dump( \get_query_var( 'pronamic_twinfield_route', null ) );
var_dump( $response->get_data() );
var_dump( $response );

get_footer();
