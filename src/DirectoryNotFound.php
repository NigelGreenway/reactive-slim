<?php
declare(strict_types=1);
namespace ReactiveSlim;

use Error;

final class DirectoryNotFound extends Error
{
    /**
     * @param string $invalidDirectory
     */
    public function __construct(string $invalidDirectory)
    {
        parent::__construct(
            sprintf('Unable to find the directory: %s', $invalidDirectory)
        );
    }
}
