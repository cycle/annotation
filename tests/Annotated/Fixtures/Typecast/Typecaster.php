<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Typecast;

use Cycle\ORM\Parser\TypecastInterface;

class Typecaster implements TypecastInterface
{
    public function setRules(array $rules): array
    {
        return $rules;
    }

    public function cast(array $values): array
    {
        return $values;
    }
}
