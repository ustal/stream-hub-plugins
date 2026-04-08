<?php

namespace Ustal\StreamHub\Plugins\Tests\Unit\MessageComposer;

use PHPUnit\Framework\TestCase;
use Ustal\StreamHub\Component\Enum\StreamEventType;
use Ustal\StreamHub\Component\Identifier\IdentifierGeneratorInterface;
use Ustal\StreamHub\Plugins\MessageComposer\Command\SendMessageCommand;
use Ustal\StreamHub\Plugins\MessageComposer\Command\SendMessageCommandHandler;
use Ustal\StreamHub\Plugins\MessageComposer\Service\MessageEventFactory;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryContext;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryModelCommandBus;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;

final class SendMessageCommandHandlerTest extends TestCase
{
    public function testHandlerCreatesMessageEventAndDispatchesAppendEventCommand(): void
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
