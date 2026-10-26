<?php
/**
 * Pronamic Twinfield deploy
 *
 * @package pronamic-twinfield
 */

declare(strict_types=1);

namespace Deployer;

require 'recipe/common.php';

set( 'plugin_slug', 'pronamic-twinfield' );

set( 'build_path', './build/pronamic-twinfield' );

$deployer_import = getenv( 'DEPLOYER_IMPORT' );

if ( false !== $deployer_import && '' !== $deployer_import ) {
	import( $deployer_import );
}

task(
	'build',
	function () {
		runLocally( 'composer run build' );
	}
);

task(
	'deploy:update_code',
	function () {
		upload( '{{build_path}}/', '{{release_path}}' );
	}
);

task(
	'deploy:symlink_plugin',
	function () {
		run( 'ln -sfn {{deploy_path}}/current {{plugins_dir}}/{{plugin_slug}}' );
	}
);

after( 'deploy:symlink', 'deploy:symlink_plugin' );

task(
	'deploy',
	[
		'build',
		'deploy:prepare',
		'deploy:publish',
	]
);
