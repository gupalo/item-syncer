<?php
/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Gupalo\ItemSyncer\Tests;

use Gupalo\ItemSyncer\SyncableEntityInterface;

class FakeSyncableEntity implements SyncableEntityInterface
{
    private \DateTimeInterface $archivedAt;

    public function __construct(
        private readonly int $id,
        private string $name = '',
    ) {
    }

    public function getIndexValue(): string
    {
        return $this->id;
    }

    /** @var self $item */
    public function updateFromItem($item): ?self
    {
        $this->name = $item->name;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArchivedAt(): \DateTimeInterface
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(\DateTimeInterface $archivedAt): void
    {
        $this->archivedAt = $archivedAt;
    }
}
