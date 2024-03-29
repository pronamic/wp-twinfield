<?php
/**
 * Meta Box Authorize
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

/**
 * Plugin.
 *
 * @var Plugin $plugin Plugin
 */
$openid_connect_client = $plugin->get_openid_connect_client();

$openid_connect_client->set_state(
	\base64_encode(
		\wp_json_encode(
			[
				'plugin'  => 'pronamic-twinfield',
				'post_id' => \get_the_ID(),
			]
		)
	)
);

$url = $openid_connect_client->get_authorize_url();

\printf(
	'<a href="%s">%s</a>',
	\esc_url( $url ),
	\esc_html__( 'Connect with Twinfield', 'pronamic-twinfield' )
);
