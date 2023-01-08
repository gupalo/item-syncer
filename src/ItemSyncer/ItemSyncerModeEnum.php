<?php

namespace Gupalo\ItemSyncer;

enum ItemSyncerModeEnum
{
    case Keep;
    case Archive;
    case Remove;
}
