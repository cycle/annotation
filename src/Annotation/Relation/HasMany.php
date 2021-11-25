<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class HasMany extends Relation
{
    use InverseTrait;

    protected const TYPE = 'hasMany';

    public function __construct(
        string $target,
        /**
         * Inner key in parent entity. Defaults to the primary key.
         */
        protected array|string|null $innerKey = null,
        /**
         * Outer key name. Defaults to {parentRole}_{innerKey}.
         */
        protected array|string|null $outerKey = null,
        /**
         * Automatically save related data with parent entity.
         */
        protected bool $cascade = true,
        /**
         * Defines if the relation can be nullable (child can have no parent).
         */
        protected bool $nullable = false,
        /**
         * Additional where condition to be applied for the relation.
         */
        protected array $where = [],
        /**
         * Additional sorting rules.
         */
        protected array $orderBy = [],
        /**
         * Set to true to automatically create FK on outerKey.
         */
        protected bool $fkCreate = true,
        /**
         * FK onDelete and onUpdate action.
         *
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkAction = 'CASCADE',
        /**
         * FK onDelete action. It has higher priority than {@see $fkAction}. Defaults to {@see $fkAction}.
         *
         * @Enum({"NO ACTION", "CASCADE", "SET NULL"})
         */
        #[ExpectedValues(values: ['NO ACTION', 'CASCADE', 'SET NULL'])]
        protected ?string $fkOnDelete = null,
        /**
         * Create an index on outerKey.
         */
        protected bool $indexCreate = true,
        /**
         * Collection that will contain loaded entities.
         */
        protected ?string $collection = null,
        /**
         * Relation load approach.
         */
        #[ExpectedValues(values: ['lazy', 'eager'])]
        string $load = 'lazy',
        ?Inverse $inverse = null,
    ) {
        $this->inverse = $inverse;

        parent::__construct($target, $load);
    }
}
