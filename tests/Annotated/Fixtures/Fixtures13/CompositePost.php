<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures13;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\PrimaryKey;

/**
 * @Entity(role="compositePost")
 * @Table(
 *      columns = {
 *          "id": @Column(type="primary"),
 *          "userId": @Column(type="integer", name="user_id"),
 *          "title": @Column(type="string"),
 *      },
 *      primary = @PrimaryKey(columns={}),
 * )
 */
#[Entity(role: 'compositePost')]
#[Column(name: 'id', type: 'primary')]
#[Column(property: 'userId', type: 'integer', name: 'user_id')]
#[PrimaryKey(columns: [])]
class CompositePost
{
    protected $id;
    protected $userId;
    protected $title;
}
