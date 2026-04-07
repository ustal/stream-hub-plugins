<?php

namespace Ustal\StreamHub\Plugins\TwoColumnLayout;

use Ustal\StreamHub\Component\Plugin\AbstractStreamPlugin;
use Ustal\StreamHub\Component\Plugin\StreamPluginCSSInterface;
use Ustal\StreamHub\Component\Plugin\StreamPluginJSInterface;
use Ustal\StreamHub\Plugins\TwoColumnLayout\Widget\TwoColumnLayoutWidget;

final class TwoColumnLayoutPlugin extends AbstractStreamPlugin implements StreamPluginCSSInterface, StreamPluginJSInterface
{
    public const NAME = 'two-column-layout';

    public static function getWidgets(): array
    {
        return [
            TwoColumnLayoutWidget::class,
        ];
    }

    public static function getCSSFiles(): array
    {
        return [
            'src/TwoColumnLayout/Resources/assets/css/two-column-layout.css',
        ];
    }

    public static function getJSFiles(): array
    {
        return [
            'src/TwoColumnLayout/Resources/assets/js/two-column-layout.js',
        ];
    }
}
