<?php
/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Gupalo\ItemSyncer\Tests;

use Gupalo\ItemSyncer\ItemSyncer;
use PHPUnit\Framework\TestCase;

class ItemSyncerTest extends TestCase
{
    public function testDiffKeeping(): void
    {
        $local = [$this->item(1), $this->item(3)];
        $remote = [$this->item(1), $this->item(2)];

        $diff = (new ItemSyncer())->diffKeeping($remote, $local);

        self::assertCount(1, $diff->createdItems);
        self::assertCount(1, $diff->updatedItems);
        self::assertCount(0, $diff->archivedItems);
        self::assertCount(0, $diff->removedItems);
        self::assertCount(1, $diff->keptItems);
        self::assertSame(2, $diff->createdItems[2]->getId());
        self::assertSame(1, $diff->updatedItems[1]->getId());
        self::assertSame(3, $diff->keptItems[3]->getId());
    }

    public function testDiffKeepingAndUpdateItem(): void
    {
        $local = [$this->item(1, 'old'), $this->item(3)];
        $remote = [$this->item(1, 'new'), $this->item(2)];

        $diff = (new ItemSyncer())->diffKeeping($remote, $local);

        self::assertSame('new', $diff->updatedItems[1]->getName());
    }

    public function testDiffArchiving(): void
    {
        $local = [$this->item(1), $this->item(3)];
        $remote = [$this->item(1), $this->item(2)];

        $diff = (new ItemSyncer())->diffArchiving($remote, $local);

        self::assertCount(1, $diff->createdItems);
        self::assertCount(1, $diff->updatedItems);
        self::assertCount(1, $diff->archivedItems);
        self::assertCount(0, $diff->removedItems);
        self::assertCount(0, $diff->keptItems);
        self::assertSame(2, $diff->createdItems[2]->getId());
        self::assertSame(1, $diff->updatedItems[1]->getId());
        self::assertSame(3, $diff->archivedItems[3]->getId());
    }

    public function testDiffRemoving(): void
    {
        $local = [$this->item(1), $this->item(3)];
        $remote = [$this->item(1), $this->item(2)];

        $diff = (new ItemSyncer())->diffRemoving($remote, $local);

        self::assertCount(1, $diff->createdItems);
        self::assertCount(1, $diff->updatedItems);
        self::assertCount(0, $diff->archivedItems);
        self::assertCount(1, $diff->removedItems);
        self::assertCount(0, $diff->keptItems);
        self::assertSame(2, $diff->createdItems[2]->getId());
        self::assertSame(1, $diff->updatedItems[1]->getId());
        self::assertSame(3, $diff->removedItems[3]->getId());
    }

    public function item(int $id, string $name = ''): FakeSyncableEntity
    {
        return new FakeSyncableEntity($id, $name);
    }
}
