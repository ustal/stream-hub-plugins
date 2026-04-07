<?php

namespace Ustal\StreamHub\Plugins\DialogScaffold\Widget;

use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Enum\SlotAcceptanceMode;
use Ustal\StreamHub\Component\Render\RenderResult;
use Ustal\StreamHub\Component\ValueObject\LayoutSlot;
use Ustal\StreamHub\Component\Widget\AbstractStreamWidget;
use Ustal\StreamHub\Plugins\DialogScaffold\Enum\DialogScaffoldSlot;
use Ustal\StreamHub\Plugins\TwoColumnLayout\Enum\TwoColumnLayoutSlot;

final class DialogScaffoldWidget extends AbstractStreamWidget
{
    public static function getName(): string
    {
        return 'dialog-scaffold';
    }

    public static function getSlot(): \BackedEnum
    {
        return TwoColumnLayoutSlot::RIGHT;
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
            new LayoutSlot(DialogScaffoldSlot::TOP, SlotAcceptanceMode::ANY),
            new LayoutSlot(DialogScaffoldSlot::MIDDLE, SlotAcceptanceMode::ANY),
            new LayoutSlot(DialogScaffoldSlot::BOTTOM, SlotAcceptanceMode::ANY),
        ];
    }

    public static function getTemplates(): array
    {
        return [
            'twig' => 'src/DialogScaffold/Resources/views/twig/widget/dialog_scaffold.html.twig',
        ];
    }
}
