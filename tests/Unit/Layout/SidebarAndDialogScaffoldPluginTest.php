<?php

namespace Ustal\StreamHub\Plugins\Tests\Unit\Layout;

use PHPUnit\Framework\TestCase;
use Ustal\StreamHub\Component\Enum\DefaultSlotName;
use Ustal\StreamHub\Component\Render\ViewRendererInterface;
use Ustal\StreamHub\Component\Service\PluginDefinitionBuilder;
use Ustal\StreamHub\Component\Service\SlotTreeBuilder;
use Ustal\StreamHub\Plugins\DialogScaffold\DialogScaffoldPlugin;
use Ustal\StreamHub\Plugins\DialogScaffold\Enum\DialogScaffoldSlot;
use Ustal\StreamHub\Plugins\DialogScaffold\Widget\DialogScaffoldWidget;
use Ustal\StreamHub\Plugins\SidebarScaffold\Enum\SidebarScaffoldSlot;
use Ustal\StreamHub\Plugins\SidebarScaffold\SidebarScaffoldPlugin;
use Ustal\StreamHub\Plugins\SidebarScaffold\Widget\SidebarScaffoldWidget;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryContext;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryViewRenderer;
use Ustal\StreamHub\Plugins\TwoColumnLayout\Enum\TwoColumnLayoutSlot;
use Ustal\StreamHub\Plugins\TwoColumnLayout\TwoColumnLayoutPlugin;

final class SidebarAndDialogScaffoldPluginTest extends TestCase
{
    public function testSidebarAndDialogScaffoldsProvideNestedSlotsInsideTwoColumnLayout(): void
    {
        $registry = (new PluginDefinitionBuilder())->build(
            [
                TwoColumnLayoutPlugin::class,
                SidebarScaffoldPlugin::class,
                DialogScaffoldPlugin::class,
            ],
            [DefaultSlotName::MAIN]
        );

        $tree = (new SlotTreeBuilder())->build($registry, [DefaultSlotName::MAIN]);

        $this->assertSame(
            [SidebarScaffoldWidget::class],
            array_map(
                static fn ($assignment): string => $assignment->widgetClass,
                $tree->getAssignmentsForSlot(TwoColumnLayoutSlot::LEFT)
            )
        );
        $this->assertSame(
            [DialogScaffoldWidget::class],
            array_map(
                static fn ($assignment): string => $assignment->widgetClass,
                $tree->getAssignmentsForSlot(TwoColumnLayoutSlot::RIGHT)
            )
        );
        $this->assertSame(
            [
                SidebarScaffoldSlot::FILTER->value,
                SidebarScaffoldSlot::SEARCH->value,
                SidebarScaffoldSlot::LIST->value,
            ],
            $tree->getChildSlots(TwoColumnLayoutSlot::LEFT)
        );
        $this->assertSame(
            [
                DialogScaffoldSlot::TOP->value,
                DialogScaffoldSlot::MIDDLE->value,
                DialogScaffoldSlot::BOTTOM->value,
            ],
            $tree->getChildSlots(TwoColumnLayoutSlot::RIGHT)
        );
    }

    public function testScaffoldWidgetsUseTwigTemplates(): void
    {
        $renderer = new InMemoryViewRenderer('twig', '<div>ok</div>');
        $context = new InMemoryContext(values: [
            ViewRendererInterface::class => $renderer,
        ]);

        $sidebar = (new SidebarScaffoldWidget())->render($context);
        $this->assertSame('<div>ok</div>', $sidebar->html);
        $this->assertSame(
            'src/SidebarScaffold/Resources/views/twig/widget/sidebar_scaffold.html.twig',
            $renderer->lastTemplate
        );

        $dialog = (new DialogScaffoldWidget())->render($context);
        $this->assertSame('<div>ok</div>', $dialog->html);
        $this->assertSame(
            'src/DialogScaffold/Resources/views/twig/widget/dialog_scaffold.html.twig',
            $renderer->lastTemplate
        );
    }
}
