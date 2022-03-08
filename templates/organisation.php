<?php

get_header();

?>
<dl>
	<dt><?php esc_html_e( 'Code', 'pronamic-twinfield' ); ?></dt>
	<dd><code><?php echo esc_html( $organisation->get_code() ); ?></code></dd>

	<dt><abbr title="<?php esc_attr_e( 'Universally Unique Identifier', 'pronamic-twinfield' ); ?>"><?php esc_html_e( 'UUID', 'pronamic-twinfield' ); ?></abbr></dt>
	<dd><code><?php echo esc_html( $organisation->get_uuid() ); ?></code></dd>
</dl>

<?php

get_footer();
