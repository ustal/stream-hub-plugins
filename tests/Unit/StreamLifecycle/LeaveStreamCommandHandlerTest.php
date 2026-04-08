<?php

namespace Ustal\StreamHub\Plugins\Tests\Unit\StreamLifecycle;

use PHPUnit\Framework\TestCase;
use Ustal\StreamHub\Component\Enum\StreamEventType;
use Ustal\StreamHub\Component\Identifier\Generator\UuidV7IdentifierGenerator;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\LeaveStreamCommand as CoreLeaveStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\LeaveStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\LeaveStreamCommandHandler;
use Ustal\StreamHub\Plugins\StreamLifecycle\Service\LifecycleSystemEventFactory;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryContext;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryModelCommandBus;

final class LeaveStreamCommandHandlerTest extends TestCase
{
    public function testItAppendsSystemEventAndLeavesStream(): void
    {
        $modelBus = new InMemoryModelCommandBus();
        $handler = new LeaveStreamCommandHandler(
            $modelBus,
            new LifecycleSystemEventFactory(new UuidV7IdentifierGenerator())
        );
        $context = new InMemoryContext('user-1');

        $handler->handle(new LeaveStreamCommand('stream-42', 'Alice'), $context);

        self::assertCount(2, $modelBus->commands);
        self::assertInstanceOf(AppendStreamEventCommand::class, $modelBus->commands[0]);
        self::assertInstanceOf(CoreLeaveStreamCommand::class, $modelBus->commands[1]);

        /** @var AppendStreamEventCommand $appendCommand */
        $appendCommand = $modelBus->commands[0];
        /** @var CoreLeaveStreamCommand $leaveCommand */
        $leaveCommand = $modelBus->commands[1];

        self::assertSame('stream-42', $appendCommand->streamId);
        self::assertSame(StreamEventType::SYSTEM, $appendCommand->event->type);
        self::assertSame('Alice decided to end the dialog.', $appendCommand->event->content);
        self::assertSame('stream-42', $leaveCommand->streamId);
        self::assertSame('user-1', $leaveCommand->userId);
        self::assertInstanceOf(\DateTimeImmutable::class, $leaveCommand->leftAt);
    }
}
