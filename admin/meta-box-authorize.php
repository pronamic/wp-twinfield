<?php

namespace Pronamic\WordPress\Twinfield\Plugin;

$openid_connect_client = $plugin->get_openid_connect_client();

$state = (object) array(
	'redirect_uri' => \rest_url( 'pronamic-twinfield/v1/authorize/' . get_the_ID() ),
);

$url = $openid_connect_client->get_authorize_url( $state );

\printf(
	'<a href="%s">%s</a>',
	\esc_url( $url ),
	\esc_html__( 'Connect with Twinfield', 'pronamic-twinfield' )
);
