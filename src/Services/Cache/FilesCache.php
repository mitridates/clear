<?php

namespace App\Services\Cache;

use Symfony\Component\Cache\Adapter\FilesystemTagAwareAdapter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class FilesCache implements CacheInterface
{
    public FilesystemTagAwareAdapter $cache;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        string $projectDir,
        #[Autowire('%kernel.environment%')]
        string $env
    ) {
        $this->cache = new FilesystemTagAwareAdapter(
        // a string used as the subdirectory of the root cache directory, where cache
        // items will be stored
            'FilesystemCache',
            // the default lifetime (in seconds) for cache items that do not define their
            // own lifetime, with a value 0 causing items to be stored indefinitely (i.e.
            // until the files are deleted)
            $TTL = 0,
            // the main cache directory (the application needs read-write permissions on it)
            // if none is specified, a directory is created inside the system temporary directory
            $projectDir . DIRECTORY_SEPARATOR . "var/cache/$env"
        );
    }
}