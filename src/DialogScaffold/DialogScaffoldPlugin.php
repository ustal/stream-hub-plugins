<?php

namespace Ustal\StreamHub\Plugins\DialogScaffold;

use Ustal\StreamHub\Component\Plugin\AbstractStreamPlugin;
use Ustal\StreamHub\Plugins\DialogScaffold\Widget\DialogScaffoldWidget;

final class DialogScaffoldPlugin extends AbstractStreamPlugin
{
    public const NAME = 'dialog-scaffold';

    public static function getWidgets(): array
    {
        return [
            DialogScaffoldWidget::class,
        ];
    }
}
