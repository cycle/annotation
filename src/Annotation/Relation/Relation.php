<?php

declare(strict_types=1);

namespace Cycle\Annotated\Annotation\Relation;

use Doctrine\Common\Annotations\Annotation\Enum;
use JetBrains\PhpStorm\ExpectedValues;

abstract class Relation implements RelationInterface
{
    // relation type
    protected const TYPE = '';

    public function __construct(
        /**
         * @var non-empty-string|null
         */
        protected ?string $target,
        /**
         * @Enum({"eager", "lazy", "promise"})
         * @var non-empty-string|null
         */
        #[ExpectedValues(values: ['lazy', 'eager'])]
        protected string $load = 'lazy',
    ) {
    }

    public function getType(): string
    {
        return static::TYPE;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function getLoad(): ?string
    {
        return $this->load;
    }

    public function getOptions(): array
    {
        $options = get_object_vars($this);
        unset($options['target'], $options['inverse']);

        return $options;
    }
}
