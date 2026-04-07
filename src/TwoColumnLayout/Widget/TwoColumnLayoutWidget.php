<?php

namespace Ustal\StreamHub\Plugins\TwoColumnLayout\Widget;

use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Enum\DefaultSlotName;
use Ustal\StreamHub\Component\Enum\SlotAcceptanceMode;
use Ustal\StreamHub\Component\Render\RenderResult;
use Ustal\StreamHub\Component\ValueObject\LayoutSlot;
use Ustal\StreamHub\Component\Widget\AbstractStreamWidget;
use Ustal\StreamHub\Plugins\TwoColumnLayout\Enum\TwoColumnLayoutSlot;

final class TwoColumnLayoutWidget extends AbstractStreamWidget
{
    public static function getName(): string
    {
        return 'two-column-layout';
    }

    public static function getSlot(): \BackedEnum
    {
        return DefaultSlotName::MAIN;
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
            new LayoutSlot(TwoColumnLayoutSlot::LEFT, SlotAcceptanceMode::ANY),
            new LayoutSlot(TwoColumnLayoutSlot::RIGHT, SlotAcceptanceMode::ANY),
        ];
    }

    public static function getTemplates(): array
    {
        return [
            'twig' => 'src/TwoColumnLayout/Resources/views/twig/widget/two_column_layout.html.twig',
        ];
    }
}
