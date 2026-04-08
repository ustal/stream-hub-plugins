<?php

namespace Ustal\StreamHub\Plugins\Tests\Unit\StreamLifecycle;

use PHPUnit\Framework\TestCase;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\JoinStreamCommand as CoreJoinStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\JoinStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Command\JoinStreamCommandHandler;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryContext;
use Ustal\StreamHub\Plugins\Tests\Fake\InMemoryModelCommandBus;

final class JoinStreamCommandHandlerTest extends TestCase
{
    public function testItDispatchesLowLevelJoinStreamCommand(): void
    {
        $modelBus = new InMemoryModelCommandBus();
        $handler = new JoinStreamCommandHandler($modelBus);
        $context = new InMemoryContext('user-2');

        $handler->handle(
            new JoinStreamCommand(
                streamId: 'stream-42',
                displayName: 'Bob',
                settings: ['role' => 'guest'],
            ),
            $context
        );

        self::assertCount(1, $modelBus->commands);
        self::assertInstanceOf(CoreJoinStreamCommand::class, $modelBus->commands[0]);

        /** @var CoreJoinStreamCommand $joinCommand */
        $joinCommand = $modelBus->commands[0];

        self::assertSame('stream-42', $joinCommand->streamId);
        self::assertSame('user-2', $joinCommand->participant->userId);
        self::assertSame('Bob', $joinCommand->participant->displayName);
        self::assertTrue($joinCommand->participant->active);
        self::assertSame(['role' => 'guest'], $joinCommand->participant->settings);
    }
}
