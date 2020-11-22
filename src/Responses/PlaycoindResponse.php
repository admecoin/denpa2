<?php

declare(strict_types=1);

namespace Denpaw\Playcoin\Responses;

use Denpaw\Playcoin\Traits\Collection;
use Denpaw\Playcoin\Traits\ImmutableArray;
use Denpaw\Playcoin\Traits\SerializableContainer;

class PlaycoindResponse extends Response implements
    \ArrayAccess,
    \Countable,
    \Serializable,
    \JsonSerializable
{
    use Collection;
    use ImmutableArray;
    use SerializableContainer;

    /**
     * Gets array representation of response object.
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this->result();
    }

    /**
     * Gets root container of response object.
     *
     * @return array
     */
    public function toContainer(): array
    {
        return $this->container;
    }
}
