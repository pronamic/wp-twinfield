<?php
/**
 * Meta box save schedule
 *
 * @author Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @package Pronamic/WordPress/Twinfield
 */

namespace Pronamic\WordPress\Twinfield;

?>
<table>
	<thead>
		<tr>
			<th scope="col"><?php \esc_html_e( 'Entity', 'pronamic-twinfield' ); ?></th>
			<th scope="col"><?php \esc_html_e( 'Schedule', 'pronamic-twinfield' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<th scope="row"><?php \esc_html_e( 'Offices', 'pronamic-twinfield' ); ?></th>
			<td>
				<input type="text" name="_pronamic_twinfield_save_offices_schedule" value="" />
			</td>
		</tr>
		<tr>
			<th scope="row"><?php \esc_html_e( 'Hierarchies', 'pronamic-twinfield' ); ?></th>
			<td>
				<input type="text" name="_pronamic_twinfield_save_hierarchies_schedule" value="" />
			</td>
		</tr>
	</tbody>
</table>
