<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Attribute;

use Cycle\Annotated\Annotation\Column;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    #[Column('integer', nullable: true, unsigned: true)]
    private $column1;

    #[Column('smallInt', unsigned: true, zerofill: true)]
    private $column2;

    #[Column('string(32)', size: 128)]
    private $column3;

    #[Column('string(32)', readonlySchema: true)]
    private mixed $column4;

    public function testOneAttribute(): void
    {
        $attr = $this->getAttribute('column1');

        $this->assertSame(['unsigned' => true], $attr->getAttributes());
    }

    public function testTwoAttributes(): void
    {
        $attr = $this->getAttribute('column2');

        $this->assertSame(['unsigned' => true, 'zerofill' => true], $attr->getAttributes());
    }

    public function testCustomSizeAttribute(): void
    {
        $attr = $this->getAttribute('column3');

        $this->assertSame(['size' => 128], $attr->getAttributes());
    }

    public function testDefaultReadonlySchema(): void
    {
        $attr = $this->getAttribute('column1');

        $this->assertFalse($attr->isReadonlySchema());
    }

    public function testReadonlySchema(): void
    {
        $attr = $this->getAttribute('column4');

        $this->assertTrue($attr->isReadonlySchema());
    }

    private function getAttribute(string $field): Column
    {
        $ref = new \ReflectionClass(static::class);
        return $ref->getProperty($field)->getAttributes(Column::class)[0]->newInstance();
    }
}
