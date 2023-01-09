<?php
/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Gupalo\ItemSyncer\Tests;

use Gupalo\ItemSyncer\ItemSyncerDiff;
use PHPUnit\Framework\TestCase;

class ItemSyncerDiffTest extends TestCase
{
    public function testStat(): void
    {
        $r = new ItemSyncerDiff(
            createdItems: [new FakeSyncableEntity(1)],
            updatedItems: [new FakeSyncableEntity(2), new FakeSyncableEntity(3)],
            archivedItems: [new FakeSyncableEntity(4), new FakeSyncableEntity(5), new FakeSyncableEntity(6)],
            removedItems: [new FakeSyncableEntity(7), new FakeSyncableEntity(8), new FakeSyncableEntity(9), new FakeSyncableEntity(10)],
            keptItems: [],
        );

        self::assertSame(['created' => 1, 'updated' => 2, 'archived' => 3, 'removed' => 4], $r->stat());
    }

    public function testStatFull(): void
    {
        $r = new ItemSyncerDiff(
            createdItems: [new FakeSyncableEntity(1)],
            updatedItems: [new FakeSyncableEntity(2), new FakeSyncableEntity(3)],
            archivedItems: [new FakeSyncableEntity(4), new FakeSyncableEntity(5), new FakeSyncableEntity(6)],
            removedItems: [new FakeSyncableEntity(7), new FakeSyncableEntity(8), new FakeSyncableEntity(9), new FakeSyncableEntity(10)],
            keptItems: [],
        );

        self::assertSame(['created' => 1, 'updated' => 2, 'archived' => 3, 'removed' => 4, 'kept' => 0], $r->statFull());
    }
}
