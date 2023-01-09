<?php

namespace Gupalo\ItemSyncer;

class ItemSyncerDiff
{
    public function __construct(
        /** @var SyncableEntityInterface[] */ public readonly array $createdItems = [],
        /** @var SyncableEntityInterface[] */ public readonly array $updatedItems = [],
        /** @var SyncableEntityInterface[] */ public readonly array $archivedItems = [],
        /** @var SyncableEntityInterface[] */ public readonly array $removedItems = [],
        /** @var SyncableEntityInterface[] */ public readonly array $keptItems = [],
    ) {
    }

    public function getUpdatedItems(): array
    {
        return array_merge(
            $this->archivedItems,
            $this->updatedItems,
            $this->createdItems,
        );
    }

    public function stat(): array
    {
        return array_filter($this->statFull());
    }

    public function statFull(): array
    {
        return [
            'created' => count($this->createdItems),
            'updated' => count($this->updatedItems),
            'archived' => count($this->archivedItems),
            'removed' => count($this->removedItems),
            'kept' => count($this->keptItems),
        ];
    }
}
