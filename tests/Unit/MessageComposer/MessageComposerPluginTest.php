<?php

namespace Ustal\StreamHub\Plugins\Tests\Unit\MessageComposer;

use PHPUnit\Framework\TestCase;
use Ustal\StreamHub\Component\Enum\DefaultSlotName;
use Ustal\StreamHub\Component\Enum\StreamEventType;
use Ustal\StreamHub\Component\Identifier\IdentifierGeneratorInterface;
use Ustal\StreamHub\Component\Render\ViewRendererInterface;
use Ustal\StreamHub\Component\Service\PluginDefinitionBuilder;
use Ustal\StreamHub\Component\Service\SlotTreeBuilder;
use Ustal\StreamHub\Plugins\DialogScaffold\DialogScaffoldPlugin;
use Ustal\StreamHub\Plugins\DialogScaffold\Enum\DialogScaffoldSlot;
use Ustal\StreamHub\Plugins\MessageComposer\Command\SendMessageCommand;
use Ustal\StreamHub\Plugins\MessageComposer\Command\SendMessageCommandHandler;
use Ustal\StreamHub\Plugins\MessageComposer\MessageComposerPlugin;
use Ustal\StreamHub\Plugins\MessageComposer\Service\MessageEventFactory;
use Ustal\StreamHub\Plugins\MessageComposer\Widget\MessageComposerWidget;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryContext;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryModelCommandBus;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryViewRenderer;
use Ustal\StreamHub\Plugins\TwoColumnLayout\TwoColumnLayoutPlugin;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;

final class MessageComposerPluginTest extends TestCase
{
    public function testMessageComposerDefaultsToDialogBottomSlot(): void
    {
        $registry = (new PluginDefinitionBuilder())->build(
            [
                TwoColumnLayoutPlugin::class,
                DialogScaffoldPlugin::class,
                MessageComposerPlugin::class,
            ],
            [DefaultSlotName::MAIN]
        );

        $tree = (new SlotTreeBuilder())->build($registry, [DefaultSlotName::MAIN]);

        $this->assertSame(
            ['event_id'],
            $registry->get(MessageComposerPlugin::getName())->identifierGeneratorRequirements
        );
        $this->assertSame(
            [MessageComposerWidget::class],
            array_map(
                static fn ($assignment): string => $assignment->widgetClass,
                $tree->getAssignmentsForSlot(DialogScaffoldSlot::BOTTOM)
            )
        );
    }

    public function testMessageComposerWidgetUsesTwigTemplateAndContextValues(): void
    {
        $renderer = new InMemoryViewRenderer('twig', '<form>ok</form>');
        $context = new InMemoryContext(values: [
            ViewRendererInterface::class => $renderer,
            MessageComposerWidget::ACTION_URL_CONTEXT_KEY => '/streams/stream-1/message',
            MessageComposerWidget::STREAM_ID_CONTEXT_KEY => 'stream-1',
            MessageComposerWidget::PLACEHOLDER_CONTEXT_KEY => 'Type here',
            MessageComposerWidget::SUBMIT_LABEL_CONTEXT_KEY => 'Reply',
            MessageComposerWidget::MESSAGE_FIELD_NAME_CONTEXT_KEY => 'content',
        ]);

        $widget = new MessageComposerWidget();

        $this->assertTrue($widget->isVisible($context));
        $this->assertSame('<form>ok</form>', $widget->render($context)->html);
        $this->assertSame(
            'src/MessageComposer/Resources/views/twig/widget/message_composer.html.twig',
            $renderer->lastTemplate
        );
        $this->assertSame('/streams/stream-1/message', $renderer->lastContext['action_url']);
        $this->assertSame('stream-1', $renderer->lastContext['stream_id']);
        $this->assertSame('Type here', $renderer->lastContext['placeholder']);
        $this->assertSame('Reply', $renderer->lastContext['submit_label']);
        $this->assertSame('content', $renderer->lastContext['message_field_name']);
        $this->assertSame('csrf-stream_hub_send_message', $renderer->lastContext['csrf_token']);
    }

    public function testMessageComposerHandlerCreatesMessageEventAndDispatchesAppendEventCommand(): void
    {
        $modelCommandBus = new InMemoryModelCommandBus();
        $generator = new class implements IdentifierGeneratorInterface {
            public function generate(): string
            {
                return 'event-123';
            }
        };
        $handler = new SendMessageCommandHandler($modelCommandBus, new MessageEventFactory($generator));
        $context = new InMemoryContext(userId: 'user-42');

        $handler->handle(
            new SendMessageCommand(streamId: 'stream-7', content: '  Hello world  '),
            $context
        );

        $this->assertSame($context, $modelCommandBus->lastContext);
        $this->assertInstanceOf(AppendStreamEventCommand::class, $modelCommandBus->lastCommand);
        $this->assertSame($context, $modelCommandBus->lastCommand->context);
        $this->assertSame('stream-7', $modelCommandBus->lastCommand->streamId);
        $this->assertSame('event-123', $modelCommandBus->lastCommand->event->id);
        $this->assertSame('stream-7', $modelCommandBus->lastCommand->event->streamId);
        $this->assertSame('user-42', $modelCommandBus->lastCommand->event->userId);
        $this->assertSame(StreamEventType::MESSAGE, $modelCommandBus->lastCommand->event->type);
        $this->assertSame('Hello world', $modelCommandBus->lastCommand->event->content);
    }
}
