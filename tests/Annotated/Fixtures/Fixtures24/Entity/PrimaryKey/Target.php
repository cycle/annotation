<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures24\Entity\PrimaryKey;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity(table="to")
 */
class Target
{
    /**
     * @Column(type="primary")
     */
    public int $id;
}
