<?php

namespace Ustal\StreamHub\Plugins\SidebarScaffold\Enum;

enum SidebarScaffoldSlot: string
{
    case FILTER = 'sidebar.filter';
    case SEARCH = 'sidebar.search';
    case LIST = 'sidebar.list';
}
