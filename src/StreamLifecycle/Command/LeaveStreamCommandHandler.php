<?php

namespace Ustal\StreamHub\Plugins\StreamLifecycle\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandHandlerInterface;
use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;
use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Core\Command\ModelCommandBusInterface;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\LeaveStreamCommand as CoreLeaveStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Service\LifecycleSystemEventFactory;

final readonly class LeaveStreamCommandHandler implements StreamCommandHandlerInterface
{
    public function __construct(
        private ModelCommandBusInterface $modelCommandBus,
        private LifecycleSystemEventFactory $eventFactory,
    ) {}

    public function handle(StreamCommandInterface $command, StreamContextInterface $context): void
    {
        if (!$command instanceof LeaveStreamCommand) {
            throw new \LogicException('Unexpected command type.');
        }

        $leftAt = new \DateTimeImmutable();

        $this->modelCommandBus->handle(
            new AppendStreamEventCommand(
                $context,
                $command->streamId,
                $this->eventFactory->createStreamLeftEvent($command, $context, $leftAt)
            ),
            $context
        );

        $this->modelCommandBus->handle(
            new CoreLeaveStreamCommand(
                $context,
                $command->streamId,
                $context->getUserId(),
                $leftAt
            ),
            $context
        );
    }

    public static function supports(): string
    {
        return LeaveStreamCommand::class;
    }
}
