<?php

declare(strict_types=1);

use JetBrains\PhpStorm\Pure;

final class LimitRequestExceededException extends \RuntimeException
{
    #[Pure]
    public function __construct(int $maxTries)
    {
        parent::__construct(message: \sprintf("Limit request exceeded, max tries: %s", $maxTries));
    }
}