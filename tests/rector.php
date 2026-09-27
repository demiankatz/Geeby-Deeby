<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;

return RectorConfig::configure()
    ->withCache(
        cacheClass: FileCacheStorage::class,
        cacheDirectory: __DIR__ . '/../.rector'
    )->withPaths([
        __DIR__ . '/../config',
        __DIR__ . '/../module',
        __DIR__ . '/../public',
    ])
    ->withComposerBased(
        doctrine: true,
        phpunit: true,
    )
    ->withSkip([
        // This method causes a breaking change in \GeebyDeeby\Db\EntityManagerFactory; that
        // change won't be safe to make until we raise the minimum PHP version to 8.4. We
        // should remove this exclusion at that time.
        RenameMethodRector::class,
    ])
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(6)
    ->withCodeQualityLevel(22);
