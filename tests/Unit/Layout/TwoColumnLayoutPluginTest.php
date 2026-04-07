<?php

namespace Ustal\StreamHub\Plugins\Tests\Unit\Layout;

use PHPUnit\Framework\TestCase;
use Ustal\StreamHub\Component\Enum\DefaultSlotName;
use Ustal\StreamHub\Component\Exception\TransformationException;
use Ustal\StreamHub\Component\Render\ViewRendererInterface;
use Ustal\StreamHub\Component\Service\PluginDefinitionBuilder;
use Ustal\StreamHub\Component\Service\PluginManager;
use Ustal\StreamHub\Component\Service\SlotTreeBuilder;
use Ustal\StreamHub\Component\ValueObject\LayoutSlot;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryContext;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryViewRenderer;
use Ustal\StreamHub\Plugins\TwoColumnLayout\Enum\TwoColumnLayoutSlot;
use Ustal\StreamHub\Plugins\TwoColumnLayout\TwoColumnLayoutPlugin;
use Ustal\StreamHub\Plugins\TwoColumnLayout\Widget\TwoColumnLayoutWidget;

final class TwoColumnLayoutPluginTest extends TestCase
{
    public function testPluginDeclaresSingleContainerWidgetAndCssAsset(): void
    {
        $this->assertSame([TwoColumnLayoutWidget::class], TwoColumnLayoutPlugin::getWidgets());
        $this->assertSame(
            ['src/TwoColumnLayout/Resources/assets/css/two-column-layout.css'],
            TwoColumnLayoutPlugin::getCSSFiles()
        );
        $this->assertSame(
            ['src/TwoColumnLayout/Resources/assets/js/two-column-layout.js'],
            TwoColumnLayoutPlugin::getJSFiles()
        );
    }

    public function testWidgetProvidesLeftAndRightSlotsUnderMain(): void
    {
        $registry = (new PluginDefinitionBuilder())->build(
            [TwoColumnLayoutPlugin::class],
            [DefaultSlotName::MAIN]
        );

        $tree = (new SlotTreeBuilder())->build($registry, [DefaultSlotName::MAIN]);

        $this->assertTrue($tree->hasSlot(DefaultSlotName::MAIN));
        $this->assertTrue($tree->hasSlot(TwoColumnLayoutSlot::LEFT));
        $this->assertTrue($tree->hasSlot(TwoColumnLayoutSlot::RIGHT));
        $this->assertSame(
            [TwoColumnLayoutWidget::class],
            array_map(
                static fn ($assignment): string => $assignment->widgetClass,
                $tree->getAssignmentsForSlot(DefaultSlotName::MAIN)
            )
        );
        $this->assertSame(
            [TwoColumnLayoutSlot::LEFT->value, TwoColumnLayoutSlot::RIGHT->value],
            $tree->getChildSlots(DefaultSlotName::MAIN)
        );
    }

    public function testWidgetRendersUsingRendererFromContext(): void
    {
        $renderer = new InMemoryViewRenderer('twig', '<div>layout</div>');
        $widget = new TwoColumnLayoutWidget();
        $result = $widget->render(new InMemoryContext(values: [
            ViewRendererInterface::class => $renderer,
        ]));

        $this->assertSame('<div>layout</div>', $result->html);
        $this->assertSame(
            'src/TwoColumnLayout/Resources/views/twig/widget/two_column_layout.html.twig',
            $renderer->lastTemplate
        );
    }

    public function testWidgetFailsWithoutConfiguredRenderer(): void
    {
        $this->expectException(TransformationException::class);

        (new TwoColumnLayoutWidget())->render(new InMemoryContext());
    }

    public function testPluginManagerExposesCssAssetForBundleLevelPublishing(): void
    {
        $registry = (new PluginDefinitionBuilder())->build(
            [TwoColumnLayoutPlugin::class],
            [DefaultSlotName::MAIN]
        );
        $manager = new PluginManager($registry);

        $this->assertSame(
            [
                'class' => TwoColumnLayoutPlugin::class,
                'name' => TwoColumnLayoutPlugin::getName(),
                'js' => ['src/TwoColumnLayout/Resources/assets/js/two-column-layout.js'],
                'css' => ['src/TwoColumnLayout/Resources/assets/css/two-column-layout.css'],
            ],
            $manager->getPublicAssets()[TwoColumnLayoutPlugin::getName()]
        );
    }

    public function testWidgetProvidesLayoutSlotsAsValueObjects(): void
    {
        $slots = TwoColumnLayoutWidget::provideSlots();

        $this->assertCount(2, $slots);
        $this->assertContainsOnlyInstancesOf(LayoutSlot::class, $slots);
    }
}
