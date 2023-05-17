<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL\Relation\Morphed;

// phpcs:ignore
use Cycle\Annotated\Tests\Functional\Driver\Common\Relation\Morphed\MorphedHasOneTestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-mysql')]
final class MorphedHasOneTest extends MorphedHasOneTestCase
{
    public const DRIVER = 'mysql';
}
