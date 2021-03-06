<?php

// @see https://github.com/WordPress/WordPress/blob/3.8/wp-admin/options-permalink.php#L237
?>
<p>
	<?php

	$url = home_url( $this->plugin->get_url_prefix() . '/' . _x( 'invoices', 'slug', 'twinfield' ) );

	printf( // xss ok
		/* translators: 1: Default slug in <code> element, 2: Default URL in <code> element */
		__( 'If you like, you may enter custom structures for your invoice and customer %1$s here. For example, using %2$s as your invoice base would make your invoice links like %3$s. If you leave these blank the defaults will be used.', 'twinfield' ),
		'<abbr title="Universal Resource Locator">URL</abbr>s',
		'<code>' . esc_html( _x( 'invoices', 'slug', 'twinfield' ) ) . '</code>',
		'<code>' . esc_html( $url ) . '/140001/</code>'
	); // xss ok

	?>
</p>
