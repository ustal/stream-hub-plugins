<?php

namespace Ustal\StreamHub\Plugins\StreamLifecycle\Command;

use Ustal\StreamHub\Component\CommandBus\StreamCommandHandlerInterface;
use Ustal\StreamHub\Component\CommandBus\StreamCommandInterface;
use Ustal\StreamHub\Component\Context\StreamContextInterface;
use Ustal\StreamHub\Component\Model\StreamParticipant;
use Ustal\StreamHub\Core\Command\ModelCommandBusInterface;
use Ustal\StreamHub\Core\Plugins\CoreStream\Command\JoinStreamCommand as CoreJoinStreamCommand;

final readonly class JoinStreamCommandHandler implements StreamCommandHandlerInterface
{
    public function __construct(private ModelCommandBusInterface $modelCommandBus)
    {
    }

    public function handle(StreamCommandInterface $command, StreamContextInterface $context): void
    {
        if (!$command instanceof JoinStreamCommand) {
            throw new \LogicException('Unexpected command type.');
        }

        $participant = new StreamParticipant(
            userId: $context->getUserId(),
            displayName: $command->displayName,
            active: true,
            createdAt: new \DateTimeImmutable(),
            settings: $command->settings,
        );

        $this->modelCommandBus->handle(
            new CoreJoinStreamCommand($context, $command->streamId, $participant),
            $context
        );
    }

    public static function supports(): string
    {
        return JoinStreamCommand::class;
    }
}
