<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Inheritance;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Annotated\Tests\Fixtures\Fixtures16\Executive;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTestCase;
use Cycle\Annotated\Tests\Traits\TableTrait;
use Cycle\Database\Schema\AbstractForeignKey;
use Cycle\Database\Schema\AbstractIndex;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class JoinedTableTestCase extends BaseTestCase
{
    use TableTrait;

    #[DataProvider('allReadersProvider')]
    public function testTableInheritance(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));

        $em = new EntityManager($this->orm);

        $executive = new Executive();
        $executive->bonus = 15000;
        $executive->name = 'executive';
        $executive->salary = 50000;
        $executive->hidden = 'secret';
        $executive->type = 'executive';
        $executive->proxyFieldWithAnnotation = 'value';

        $em->persist($executive);
        $em->run();

        $this->orm->getHeap()->clean();

        $loadedExecutive = $this->orm->getRepository(Executive::class)->findByPK($executive->getFooId());
        $this->assertInstanceOf(Executive::class, $loadedExecutive);
        $this->assertSame('executive', $loadedExecutive->getName());
        $this->assertSame(50000, $loadedExecutive->getSalary());
        $this->assertSame('secret', $loadedExecutive->hidden);
        $this->assertSame(15000, $loadedExecutive->bonus);
        $this->assertSame('executive', $loadedExecutive->getType());
        $this->assertNull($loadedExecutive->proxyFieldWithAnnotation);
    }

    #[DataProvider('allReadersProvider')]
    public function testAddForeignKeys(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));
        $db = $this->dbal->database();

        $suppliersFk = $db->table('suppliers')->getForeignKeys();
        $executivesFk = $db->table('executives')->getForeignKeys();
        $externalSuppliersFk = $db->table('external_suppliers')->getForeignKeys();

        // simple case. Supplier -> Person
        $this->assertCount(1, $suppliersFk);
        $this->assertInstanceOf(AbstractForeignKey::class, reset($suppliersFk));
        $this->assertSame('people', reset($suppliersFk)->getForeignTable());
        $this->assertSame(['id'], reset($suppliersFk)->getForeignKeys());
        $this->assertSame(['id'], reset($suppliersFk)->getColumns());

        // Executive -> Employee (STI) -> Person
        $this->assertCount(1, $executivesFk);
        $this->assertInstanceOf(AbstractForeignKey::class, reset($executivesFk));
        $this->assertSame('people', reset($executivesFk)->getForeignTable());
        $this->assertSame(['id'], reset($executivesFk)->getForeignKeys());
        $this->assertSame(['id'], reset($executivesFk)->getColumns());

        // ExternalSupplier -> Supplier (JTI) -> Person. With outerKey
        $this->assertCount(1, $externalSuppliersFk);
        $this->assertInstanceOf(AbstractForeignKey::class, reset($externalSuppliersFk));
        $this->assertSame('suppliers', reset($externalSuppliersFk)->getForeignTable());
        $this->assertSame(['custom_id'], reset($externalSuppliersFk)->getForeignKeys());
        $this->assertSame(['id'], reset($externalSuppliersFk)->getColumns());
    }

    #[DataProvider('allReadersProvider')]
    public function testNotAddForeignKey(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));

        $this->assertCount(0, $this->dbal->database()->table('buyers')->getForeignKeys());
    }

    #[DataProvider('allReadersProvider')]
    public function testAddIndex(ReaderInterface $reader): void
    {
        $this->orm = $this->orm->with(new Schema($this->compile($reader)));

        $indexes = $this->dbal->database()->table('suppliers')->getIndexes();

        // remove pk index
        $indexes = array_filter($indexes, fn(AbstractIndex $index) => $index->getColumns() !== ['id']);

        // one index added automatically, one added manual
        $this->assertCount(2, $indexes);

        $this->assertTrue($this->dbal->database()->table('suppliers')->hasIndex(['index_id']));
        $this->assertTrue($this->dbal->database()->table('suppliers')->hasIndex(['custom_id']));

        $this->assertTrue(reset($indexes)->isUnique());
        $this->assertTrue(end($indexes)->isUnique());
    }

    #[DataProvider('allReadersProvider')]
    public function testJtiParentColumns(ReaderInterface $reader): void
    {
        $schema = $this->compile($reader, 'Fixtures21');

        $this->assertNotEmpty($schema);

        $this->assertArrayHasKey(SchemaInterface::COLUMNS, $schema['person']);

        // assert that parent doesn't have jti columns
        $this->assertEquals([
            'id' => 'id',
            'name' => 'name',
            'type' => 'type',
        ], $schema['person'][SchemaInterface::COLUMNS]);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->makeTable(
            table: 'people',
            columns: [
                'id' => 'primary',
                'type' => 'string',
                'name' => 'string',
                'salary' => 'float',
                'preferences' => 'string',
                'bar' => 'string',
                'stocks' => 'int',
            ],
        );

        $this->makeTable(
            table: 'executives',
            columns: [
                'id' => 'int',
                'bonus' => 'float',
                'proxy' => 'string',
                'hidden' => 'string',
            ],
            pk: ['id'],
        );

        $this->makeTable(
            table: 'suppliers',
            columns: [
                'id' => 'int',
                'custom_id' => 'int',
            ],
            pk: ['id'],
        );

        $this->makeTable(
            table: 'external_suppliers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id'],
        );

        $this->makeTable(
            table: 'local_suppliers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id'],
        );

        $this->makeTable(
            table: 'buyers',
            columns: [
                'id' => 'int',
            ],
            pk: ['id'],
        );
    }

    private function compile(ReaderInterface $reader, string $fixtures = 'Fixtures16'): array
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [sprintf(__DIR__ . '/../../../../Fixtures/%s', $fixtures)],
                'exclude' => [],
            ]),
        );

        $locator = $tokenizer->classLocator();

        return (new Compiler())->compile(new Registry($this->dbal), [
            new ResetTables(),
            new Embeddings(new TokenizerEmbeddingLocator($locator, $reader), $reader),
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new TableInheritance($reader),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);
    }
}
