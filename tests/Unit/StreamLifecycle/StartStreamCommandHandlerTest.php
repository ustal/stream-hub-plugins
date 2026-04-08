<?php

namespace Ustal\StreamHub\Plugins\Tests\Unit\StreamLifecycle;

use PHPUnit\Framework\TestCase;
use Ustal\StreamHub\Component\Enum\StreamEventType;
use Ustal\StreamHub\Component\Identifier\Generator\UuidV7IdentifierGenerator;
use Ustal\StreamHub\Component\Model\StreamParticipant;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\CreateStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\StartStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\StartStreamCommandHandler;
use Ustal\StreamHub\Plugins\StreamLifecycle\Service\LifecycleSystemEventFactory;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryContext;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryModelCommandBus;

final class StartStreamCommandHandlerTest extends TestCase
{
    public function testItCreatesStreamAndAppendsInitialSystemEvent(): void
    {
        $modelBus = new InMemoryModelCommandBus();
        $handler = new StartStreamCommandHandler(
            $modelBus,
            new LifecycleSystemEventFactory(new UuidV7IdentifierGenerator())
        );
        $context = new InMemoryContext('user-1');
        $command = new StartStreamCommand(
            'stream-42',
            [
                new StreamParticipant('user-1', 'Alice', true, new \DateTimeImmutable('-1 minute')),
                new StreamParticipant('user-2', 'Bob', true, new \DateTimeImmutable('-1 minute')),
            ]
        );

        $handler->handle($command, $context);

        self::assertCount(2, $modelBus->commands);
        self::assertInstanceOf(CreateStreamCommand::class, $modelBus->commands[0]);
        self::assertInstanceOf(AppendStreamEventCommand::class, $modelBus->commands[1]);

        /** @var AppendStreamEventCommand $appendCommand */
        $appendCommand = $modelBus->commands[1];

        self::assertSame('stream-42', $appendCommand->streamId);
        self::assertSame(StreamEventType::SYSTEM, $appendCommand->event->type);
        self::assertSame('user-1', $appendCommand->event->userId);
        self::assertSame('Alice started the conversation.', $appendCommand->event->content);
    }
}
