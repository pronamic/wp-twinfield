<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;

return RectorConfig::configure()
	->withPaths(
		[
			__DIR__ . '/admin',
			__DIR__ . '/examples',
			__DIR__ . '/export',
			__DIR__ . '/src',
			__DIR__ . '/tests',
		]
	)
	->withPhpSets()
	->withSkip(
		[
			ClassPropertyAssignToConstructorPromotionRector::class,
		]
	)
	->withTypeCoverageLevel( 0 )
	->withDeadCodeLevel( 0 )
	->withCodeQualityLevel( 0 );
