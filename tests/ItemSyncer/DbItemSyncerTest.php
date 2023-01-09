<?php
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Gupalo\ItemSyncer\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Gupalo\ItemSyncer\DbItemSyncer;
use Gupalo\ItemSyncer\ItemSyncer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class DbItemSyncerTest extends TestCase
{
    use ProphecyTrait;

    private EntityManagerInterface|ObjectProphecy $entityManager;
    private DbItemSyncer $syncer;

    protected function setUp(): void
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->syncer = new DbItemSyncer($this->entityManager->reveal(), new ItemSyncer());
    }

    public function testSyncKeeping(): void
    {
        $local = [$this->item(1), $this->item(3)];
        $remote = [$this->item(1), $this->item(2)];

        $this->entityManager->persist(Argument::that(
            static fn($v) => in_array($v->getId(), [1, 2], true)
        ))->shouldBeCalledTimes(2);
        $this->entityManager->flush()->shouldBeCalledOnce();

        $diff = $this->syncer->syncKeeping($remote, $local);

        self::assertCount(1, $diff->createdItems);
        self::assertCount(1, $diff->updatedItems);
        self::assertCount(0, $diff->archivedItems);
        self::assertCount(0, $diff->removedItems);
        self::assertCount(1, $diff->keptItems);
        self::assertSame(2, $diff->createdItems[2]->getId());
        self::assertSame(1, $diff->updatedItems[1]->getId());
        self::assertSame(3, $diff->keptItems[3]->getId());
    }

    public function testSyncArchiving(): void
    {
        $local = [$this->item(1), $this->item(3)];
        $remote = [$this->item(1), $this->item(2)];

        $this->entityManager->persist(Argument::that(
            static fn($v) => in_array($v->getId(), [1, 2, 3], true)
        ))->shouldBeCalledTimes(3);
        $this->entityManager->flush()->shouldBeCalledOnce();

        $diff = $this->syncer->syncArchiving($remote, $local);

        self::assertCount(1, $diff->createdItems);
        self::assertCount(1, $diff->updatedItems);
        self::assertCount(1, $diff->archivedItems);
        self::assertCount(0, $diff->removedItems);
        self::assertCount(0, $diff->keptItems);
        self::assertSame(2, $diff->createdItems[2]->getId());
        self::assertSame(1, $diff->updatedItems[1]->getId());
        self::assertSame(3, $diff->archivedItems[3]->getId());
        self::assertNotEmpty($diff->archivedItems[3]->getArchivedAt());
    }

    public function testSyncRemoving(): void
    {
        $local = [$this->item(1), $this->item(3)];
        $remote = [$this->item(1), $this->item(2)];

        $this->entityManager->persist(Argument::that(
            static fn($v) => in_array($v->getId(), [1, 2], true)
        ))->shouldBeCalledTimes(2);
        $this->entityManager->remove(Argument::that(
            static fn($v) => $v->getId() === 3
        ))->shouldBeCalledTimes(1);
        $this->entityManager->flush()->shouldBeCalledOnce();

        $diff = $this->syncer->syncRemoving($remote, $local);

        self::assertCount(1, $diff->createdItems);
        self::assertCount(1, $diff->updatedItems);
        self::assertCount(0, $diff->archivedItems);
        self::assertCount(1, $diff->removedItems);
        self::assertCount(0, $diff->keptItems);
        self::assertSame(2, $diff->createdItems[2]->getId());
        self::assertSame(1, $diff->updatedItems[1]->getId());
        self::assertSame(3, $diff->removedItems[3]->getId());
    }

    public function item(int $id): FakeSyncableEntity
    {
        return new FakeSyncableEntity($id);
    }
}
