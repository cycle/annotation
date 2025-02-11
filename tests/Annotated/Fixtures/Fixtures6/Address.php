<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Fixtures\Fixtures6;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\Annotation\Table\Index;

/**
 * @Embeddable(role="address", columnPrefix="address_")
 * @Table(indexes={@Index(columns={"zipcode"})})
 */
#[Embeddable(role: 'address', columnPrefix: 'address_')]
#[Table(indexes: [
    new Index(columns: ['zipcode']),
])]
class Address
{
    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $city;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $country;

    /** @Column(type="string") */
    #[Column(type: 'string')]
    protected $address;

    /** @Column(type="int") */
    #[Column(type: 'integer')]
    protected $zipcode;
}
