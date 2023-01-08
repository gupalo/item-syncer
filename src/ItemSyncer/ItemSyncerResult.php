<?php

namespace Gupalo\ItemSyncer;

use JsonSerializable;

class ItemSyncerResult implements JsonSerializable
{
    public function __construct(
        public readonly int $created = 0,
        public readonly int $updated = 0,
        public readonly int $archived = 0,
        public readonly int $removed = 0,
        public readonly int $kept = 0,
    ) {
    }

    public function stat(): array
    {
        return array_filter($this->jsonSerialize());
    }

    public function jsonSerialize(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'archived' => $this->archived,
            'removed' => $this->removed,
            'kept' => $this->kept,
        ];
    }
}
