<?php

namespace Ustal\StreamHub\Plugins\Tests\Unit\StreamLifecycle;

use PHPUnit\Framework\TestCase;
use Ustal\StreamHub\Component\Enum\StreamEventType;
use Ustal\StreamHub\Component\Identifier\IdentifierGeneratorInterface;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\CreateStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\StartStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\StartStreamCommandHandler;
use Ustal\StreamHub\Plugins\StreamLifecycle\Service\LifecycleSystemEventFactory;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryContext;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryModelCommandBus;

final class StartStreamCommandHandlerTest extends TestCase
{
    public function testItCreatesStreamAndAppendsSystemAndInitialMessageEvents(): void
    {
        $modelBus = new InMemoryModelCommandBus();
        $streamIdGenerator = new class implements IdentifierGeneratorInterface {
            public function generate(): string
            {
                return 'stream-42';
            }
        };
        $eventIdGenerator = new class implements IdentifierGeneratorInterface {
            private int $sequence = 0;

            public function generate(): string
            {
                $this->sequence++;

                return 'event-' . $this->sequence;
            }
        };

        $handler = new StartStreamCommandHandler(
            $modelBus,
            new LifecycleSystemEventFactory($eventIdGenerator)
        );
        $handler->setStreamIdGenerator($streamIdGenerator);

        $context = new InMemoryContext('user-1', ['display_name' => 'Alice']);
        $command = new StartStreamCommand(
            participantUserIds: ['user-2'],
            contextId: 'purchase-1',
            firstMessage: 'Hello Bob',
        );

        $handler->handle($command, $context);

        self::assertCount(3, $modelBus->commands);
        self::assertInstanceOf(CreateStreamCommand::class, $modelBus->commands[0]);
        self::assertInstanceOf(AppendStreamEventCommand::class, $modelBus->commands[1]);
        self::assertInstanceOf(AppendStreamEventCommand::class, $modelBus->commands[2]);

        /** @var CreateStreamCommand $createCommand */
        $createCommand = $modelBus->commands[0];
        self::assertSame('stream-42', $createCommand->streamId);
        self::assertCount(2, $createCommand->participants);
        self::assertSame('user-1', $createCommand->participants[0]->userId);
        self::assertSame('Alice', $createCommand->participants[0]->displayName);
        self::assertSame('user-2', $createCommand->participants[1]->userId);
        self::assertNull($createCommand->participants[1]->displayName);

        /** @var AppendStreamEventCommand $startedCommand */
        $startedCommand = $modelBus->commands[1];
        self::assertSame('stream-42', $startedCommand->streamId);
        self::assertSame(StreamEventType::SYSTEM, $startedCommand->event->type);
        self::assertSame('Alice started the conversation.', $startedCommand->event->content);

        /** @var AppendStreamEventCommand $messageCommand */
        $messageCommand = $modelBus->commands[2];
        self::assertSame('stream-42', $messageCommand->streamId);
        self::assertSame(StreamEventType::MESSAGE, $messageCommand->event->type);
        self::assertSame('Hello Bob', $messageCommand->event->content);
    }

    public function testItFallsBackToContextIdWhenNoStreamGeneratorIsConfigured(): void
    {
        $modelBus = new InMemoryModelCommandBus();
        $handler = new StartStreamCommandHandler(
            $modelBus,
            new LifecycleSystemEventFactory(new class implements IdentifierGeneratorInterface {
                public function generate(): string
                {
                    return 'event-1';
                }
            })
        );

        $handler->handle(
            new StartStreamCommand(contextId: 'purchase-1'),
            new InMemoryContext('user-1')
        );

        /** @var CreateStreamCommand $createCommand */
        $createCommand = $modelBus->commands[0];
        self::assertSame('purchase-1', $createCommand->streamId);
    }
}
