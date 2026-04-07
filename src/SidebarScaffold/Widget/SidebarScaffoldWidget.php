<?php

namespace Ustal\StreamHub\Plugins\SidebarScaffold\Widget;

use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Enum\SlotAcceptanceMode;
use Ustal\StreamHub\Component\Render\RenderResult;
use Ustal\StreamHub\Component\ValueObject\LayoutSlot;
use Ustal\StreamHub\Component\Widget\AbstractStreamWidget;
use Ustal\StreamHub\Plugins\SidebarScaffold\Enum\SidebarScaffoldSlot;
use Ustal\StreamHub\Plugins\TwoColumnLayout\Enum\TwoColumnLayoutSlot;

final class SidebarScaffoldWidget extends AbstractStreamWidget
{
    public static function getName(): string
    {
        return 'sidebar-scaffold';
    }

    public static function getSlot(): \BackedEnum
    {
        return TwoColumnLayoutSlot::LEFT;
    }

    public function isVisible(StreamContextInterface $context): bool
    {
        return true;
    }

    public function render(StreamContextInterface $context): RenderResult
    {
        return $this->renderTemplate($context);
    }

    public static function supports(StreamContextInterface $context): bool
    {
        return true;
    }

    public static function provideSlots(): array
    {
        return [
            new LayoutSlot(SidebarScaffoldSlot::FILTER, SlotAcceptanceMode::ANY),
            new LayoutSlot(SidebarScaffoldSlot::SEARCH, SlotAcceptanceMode::ANY),
            new LayoutSlot(SidebarScaffoldSlot::LIST, SlotAcceptanceMode::ANY),
        ];
    }

    public static function getTemplates(): array
    {
        return [
            'twig' => 'src/SidebarScaffold/Resources/views/twig/widget/sidebar_scaffold.html.twig',
        ];
    }
}
