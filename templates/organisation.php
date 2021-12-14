<?php

get_header();

?>
<dl>
	<dt><?php esc_html_e( 'Code', 'pronamic-twinfield' ); ?></dt>
	<dd><?php echo esc_html( $organisation->get_code() ); ?></dd>
</dl>

<?php

get_footer();
