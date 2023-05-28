<?php

namespace Gupalo\ItemSyncer;

enum ItemSyncerModeEnum: string
{
    case Keep = 'Keep';
    case Archive = 'Archive';
    case Remove = 'Remove';
}
