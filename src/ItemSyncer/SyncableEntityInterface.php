<?php

namespace Gupalo\ItemSyncer;

interface SyncableEntityInterface
{
    public function getIndexValue(): string;

    public function updateFromItem($item): self;
}
