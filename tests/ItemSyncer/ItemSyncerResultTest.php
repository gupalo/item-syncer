<?php

namespace Gupalo\ItemSyncer\Tests;

use Gupalo\ItemSyncer\ItemSyncerResult;
use PHPUnit\Framework\TestCase;

class ItemSyncerResultTest extends TestCase
{
    public function testStat(): void
    {
        $r = new ItemSyncerResult(
            created: 1,
            updated: 2,
            archived: 3,
            removed: 4,
            kept: 0,
        );

        self::assertSame(['created' => 1, 'updated' => 2, 'archived' => 3, 'removed' => 4], $r->stat());
    }

    public function testJsonSerialize(): void
    {
        $r = new ItemSyncerResult(
            created: 1,
            updated: 2,
            archived: 3,
            removed: 4,
            kept: 0,
        );

        self::assertSame(['created' => 1, 'updated' => 2, 'archived' => 3, 'removed' => 4, 'kept' => 0], $r->jsonSerialize());
    }
}
