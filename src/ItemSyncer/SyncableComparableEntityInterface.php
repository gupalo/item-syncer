<?php

namespace Gupalo\ItemSyncer;

interface SyncableComparableEntityInterface extends SyncableEntityInterface
{
    public function equals($item): bool;
}
