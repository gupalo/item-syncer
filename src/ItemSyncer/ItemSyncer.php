<?php

namespace Gupalo\ItemSyncer;

use Doctrine\ORM\EntityManagerInterface;

class ItemSyncer
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function syncKeeping(iterable $remoteItems, iterable $localItems): ItemSyncerResult
    {
        return $this->doSync($remoteItems, $localItems, ItemSyncerModeEnum::Keep);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function syncArchiving(iterable $remoteItems, iterable $localItems): ItemSyncerResult
    {
        return $this->doSync($remoteItems, $localItems, ItemSyncerModeEnum::Archive);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function syncRemoving(iterable $remoteItems, iterable $localItems): ItemSyncerResult
    {
        return $this->doSync($remoteItems, $localItems, ItemSyncerModeEnum::Remove);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    private function doSync(iterable $remoteItems, iterable $localItems, ItemSyncerModeEnum $actionMissing): ItemSyncerResult
    {
        $countCreated = 0;
        $countUpdated = 0;
        $countArchived = 0;
        $countRemoved = 0;
        $countKept = 0;

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
            $countCreated++;
            $this->entityManager->persist($remoteItemsIndexed[$id]);
        }

        if (in_array($actionMissing, [ItemSyncerModeEnum::Archive, ItemSyncerModeEnum::Remove], true)) {
            foreach ($archiveIds as $id) {
                $item = $localItemsIndexed[$id];
                if ($actionMissing === ItemSyncerModeEnum::Archive && method_exists($item, 'archive')) {
                    $countArchived++;
                    $item->archive();
                    $this->entityManager->persist($item);
                } elseif ($actionMissing === ItemSyncerModeEnum::Remove) {
                    $countRemoved++;
                    $this->entityManager->remove($item);
                }
            }
        } else {
            $countKept = count($archiveIds);
        }

        foreach ($updateIds as $id) {
            $countUpdated++;
            $item = $localItemsIndexed[$id];
            if ($item->updateFromItem($remoteItemsIndexed[$id])) {
                $this->entityManager->persist($item);
            }
        }

        $this->entityManager->flush();

        return new ItemSyncerResult(
            created: $countCreated,
            updated: $countUpdated,
            archived: $countArchived,
            removed: $countRemoved,
            kept: $countKept,
        );
    }
}
