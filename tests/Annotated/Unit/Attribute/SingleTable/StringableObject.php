<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Attribute\SingleTable;

use Stringable;

final class StringableObject implements Stringable
{
    public function __construct(
        private string $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
