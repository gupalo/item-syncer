<?php /** @noinspection PhpUnused */

namespace Gupalo\ItemSyncer;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class DbItemSyncer
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ItemSyncer $itemSyncer,
    ) {
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function syncKeeping(iterable $remoteItems, iterable $localItems): ItemSyncerDiff
    {
        return $this->doSync($remoteItems, $localItems, ItemSyncerModeEnum::Keep);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function syncArchiving(iterable $remoteItems, iterable $localItems): ItemSyncerDiff
    {
        return $this->doSync($remoteItems, $localItems, ItemSyncerModeEnum::Archive);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    public function syncRemoving(iterable $remoteItems, iterable $localItems): ItemSyncerDiff
    {
        return $this->doSync($remoteItems, $localItems, ItemSyncerModeEnum::Remove);
    }

    /**
     * @param SyncableEntityInterface[] $remoteItems
     * @param SyncableEntityInterface[] $localItems
     */
    private function doSync(iterable $remoteItems, iterable $localItems, ItemSyncerModeEnum $actionMissing): ItemSyncerDiff
    {
        $diff = $this->itemSyncer->diff($remoteItems, $localItems, $actionMissing);

        foreach ($diff->archivedItems as $item) {
            if (method_exists($item, 'archive')) {
                $item->archive();
            } elseif (method_exists($item, 'setArchivedAt')) {
                try {
                    $item->setArchivedAt(new DateTimeImmutable());
                } catch (Throwable) {
                    $item->setArchivedAt(new DateTime());
                }
            }
        }
        foreach ($diff->getUpdatedItems() as $item) {
            $this->entityManager->persist($item);
        }
        foreach ($diff->removedItems as $item) {
            $this->entityManager->remove($item);
        }

        $this->entityManager->flush();

        return $diff;
    }
}
