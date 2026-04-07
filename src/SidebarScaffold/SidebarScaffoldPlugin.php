<?php

namespace Ustal\StreamHub\Plugins\SidebarScaffold;

use Ustal\StreamHub\Component\Plugin\AbstractStreamPlugin;
use Ustal\StreamHub\Plugins\SidebarScaffold\Widget\SidebarScaffoldWidget;

final class SidebarScaffoldPlugin extends AbstractStreamPlugin
{
    public const NAME = 'sidebar-scaffold';

    public static function getWidgets(): array
    {
        return [
            SidebarScaffoldWidget::class,
        ];
    }
}
