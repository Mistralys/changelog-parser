<?php

declare(strict_types=1);

namespace Mistralys\ChangelogParser;

use AppUtils\OperationResult;

abstract class BaseReader extends OperationResult
{
    /**
     * @return ChangelogVersion[]
     */
    abstract public function getVersions(): array;
}
