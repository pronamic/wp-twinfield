<?php
/**
 * Settings Fields
 *
 * @since      1.0.0
 *
 * @package    Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield\Plugin;

/**
 * Settings Fields
 *
 * @since      1.0.0
 * @package    Pronamic/WordPress/Twinfield
 * @author     Remco Tolsma <info@remcotolsma.nl>
 */
class SettingFields {
	/**
	 * Array to HTML attributes
	 *
	 * @param array $attributes Attributes.
	 */
	private static function array_to_html_attributes( array $attributes ) {
		$html  = '';
		$space = '';

		foreach ( $attributes as $key => $value ) {
			$html .= $space . $key . '="' . esc_attr( $value ) . '"';

			$space = ' ';
		}

		return $html;
	}

	/**
	 * Render text
	 *
	 * @param array $attributes Attributes.
	 */
	public static function render_text( $attributes ) {
		$attributes = wp_parse_args(
			$attributes,
			[
				'id'      => '',
				'type'    => 'text',
				'name'    => '',
				'value'   => '',
				'classes' => [ 'regular-text' ],
			]
		);

		if ( isset( $attributes['label_for'] ) ) {
			$attributes['id']    = $attributes['label_for'];
			$attributes['name']  = $attributes['label_for'];
			$attributes['value'] = get_option( $attributes['label_for'] );

			unset( $attributes['label_for'] );
		}

		if ( isset( $attributes['classes'] ) ) {
			$attributes['class'] = implode( ' ', $attributes['classes'] );

			unset( $attributes['classes'] );
		}

		$description = null;
		if ( isset( $attributes['description'] ) ) {
			$description = $attributes['description'];

			unset( $attributes['description'] );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<input %s />', self::array_to_html_attributes( $attributes ) );

		if ( $description ) {
			printf(
				'<span class="description"><br />%s</span>',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$description
			);
		}
	}

	/**
	 * Render password
	 *
	 * @param array $attributes Attributes.
	 */
	public static function render_password( $attributes ) {
		$attributes['type'] = 'password';

		self::render_text( $attributes );
	}

	/**
	 * Radio buttons.
	 * 
	 * @param array $args Arguments.
	 */
	public static function radio_buttons( $args ) {
		$name    = $args['label_for'];
		$current = get_option( $name );
		$options = $args['options'];

		echo '<fieldset>';

		printf(
			'<legend class="screen-reader-text"><span>%s</span></legend>',
			'Test'
		);

		foreach ( $options as $value => $label ) {
			echo '<label>';

			printf(
				'<input type="radio" name="%s" value="%s" %s> %s',
				esc_attr( $name ),
				esc_attr( $value ),
				checked( $current, $value, false ),
				esc_html( $label )
			);

			echo '</label>';

			echo '<br />';
		}

		echo '</fieldset>';
	}
}
