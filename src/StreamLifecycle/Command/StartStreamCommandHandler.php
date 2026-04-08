<?php

namespace Ustal\StreamHub\Plugins\StreamLifecycle\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandHandlerInterface;
use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;
use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Core\Command\ModelCommandBusInterface;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\AppendStreamEventCommand;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\CreateStreamCommand;
use Ustal\StreamHub\Plugins\StreamLifecycle\Service\LifecycleSystemEventFactory;

final readonly class StartStreamCommandHandler implements StreamCommandHandlerInterface
{
    public function __construct(
        private ModelCommandBusInterface $modelCommandBus,
        private LifecycleSystemEventFactory $eventFactory,
    ) {}

    public function handle(StreamCommandInterface $command, StreamContextInterface $context): void
    {
        if (!$command instanceof StartStreamCommand) {
            throw new \LogicException('Unexpected command type.');
        }

        $this->modelCommandBus->handle(
            new CreateStreamCommand($context, $command->streamId, $command->participants),
            $context
        );

        $this->modelCommandBus->handle(
            new AppendStreamEventCommand(
                $context,
                $command->streamId,
                $this->eventFactory->createStreamStartedEvent($command, $context)
            ),
            $context
        );
    }

    public static function supports(): string
    {
        return StartStreamCommand::class;
    }
}
