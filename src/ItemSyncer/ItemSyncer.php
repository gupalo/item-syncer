<?php /** @noinspection PhpUnused */

namespace Gupalo\ItemSyncer;

use Throwable;

class ItemSyncer
{
    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function diffKeeping(iterable $remoteItems, iterable $localItems): ItemSyncerDiff
    {
        return $this->diff($remoteItems, $localItems, ItemSyncerModeEnum::Keep);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function diffArchiving(iterable $remoteItems, iterable $localItems): ItemSyncerDiff
    {
        return $this->diff($remoteItems, $localItems, ItemSyncerModeEnum::Archive);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function diffRemoving(iterable $remoteItems, iterable $localItems): ItemSyncerDiff
    {
        return $this->diff($remoteItems, $localItems, ItemSyncerModeEnum::Remove);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function diff(iterable $remoteItems, iterable $localItems, ItemSyncerModeEnum $actionMissing): ItemSyncerDiff
    {
        $createdItems = [];
        $updatedItems = [];
        $archivedItems = [];
        $removedItems = [];
        $keptItems = [];

        $remoteItemsIndexed = [];
        foreach ($remoteItems as $remoteItem) {
            $remoteItemsIndexed[$remoteItem->getIndexValue()] = $remoteItem;
        }
        $remoteIds = array_keys($remoteItemsIndexed);

        $localItemsIndexed = [];
        foreach ($localItems as $localItem) {
            $localItemsIndexed[$localItem->getIndexValue()] = $localItem;
        }
        $localIds = array_keys($localItemsIndexed);

        $createIds = array_diff($remoteIds, $localIds);
        $archiveIds = array_diff($localIds, $remoteIds);
        $updateIds = array_intersect($localIds, $remoteIds);

        foreach ($createIds as $id) {
            $createdItems[$id] = $remoteItemsIndexed[$id];
        }

        if (in_array($actionMissing, [ItemSyncerModeEnum::Archive, ItemSyncerModeEnum::Remove], true)) {
            foreach ($archiveIds as $id) {
                $item = $localItemsIndexed[$id];
                if ($actionMissing === ItemSyncerModeEnum::Archive) {
                    $isArchived = false;
                    try {
                        if (method_exists($item, 'isArchived') && $item->isArchived()) {
                            $isArchived = true;
                        }
                    } catch (Throwable) {
                    }
                    try {
                        if (!$isArchived && method_exists($item, 'getArchivedAt') && $item->getArchivedAt()) {
                            $isArchived = true;
                        }
                    } catch (Throwable) {
                    }

                    if ($isArchived) { // already archived
                        $keptItems[$id] = $localItemsIndexed[$id];
                    } else {
                        $archivedItems[$id] = $item;
                    }
                } elseif ($actionMissing === ItemSyncerModeEnum::Remove) {
                    $removedItems[$id] = $item;
                }
            }
        } else {
            foreach ($archiveIds as $id) {
                $keptItems[$id] = $localItemsIndexed[$id];
            }
        }

        foreach ($updateIds as $id) {
            $item = $localItemsIndexed[$id];

            if ($item instanceof SyncableComparableEntityInterface && $item->equals($remoteItemsIndexed[$id])) {
                $updatedItem = null;
            } else {
                $updatedItem = $item->updateFromItem($remoteItemsIndexed[$id]);
            }
            if ($updatedItem) {
                $updatedItems[$id] = $updatedItem;
            } else { // not changed
                $keptItems[$id] = $localItemsIndexed[$id];
            }
        }

        return new ItemSyncerDiff(
            createdItems: $createdItems,
            updatedItems: $updatedItems,
            archivedItems: $archivedItems,
            removedItems: $removedItems,
            keptItems: $keptItems,
        );
    }
}
