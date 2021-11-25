<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation\Morphed;

use Cycle\Annotated\Annotation\Relation\Inverse;
use Cycle\Annotated\Annotation\Relation\Relation;
use Cycle\Annotated\Annotation\Relation\Traits\InverseTrait;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class MorphedHasOne extends Relation
{
    use InverseTrait;

    protected const TYPE = 'morphedHasOne';

    public function __construct(
        string $target,
        /**
         * Automatically save related data with parent entity.
         */
        protected bool $cascade = true,
        /**
         * Defines if the relation can be nullable (child can have no parent).
         */
        protected bool $nullable = false,
        /**
         * Inner key in parent entity. Defaults to the primary key.
         */
        protected array|string|null $innerKey = null,
        /**
         * Outer key name. Defaults to `{parentRole}_{innerKey}`.
         */
        protected array|string|null $outerKey = null,
        /**
         * Name of key to store related entity role. Defaults to `{relationName}_role`.
         */
        protected string $morphKey = '{relationName}_role',
        /**
         * The length of morph key.
         */
        protected int $morphKeyLength = 32,
        /**
         * Create an index on morphKey and innerKey.
         */
        protected bool $indexCreate = true,
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
